<?php
/**
 * User Functions - All CRUD operations for regular users
 * Uses colors from root.css variables
 */

require_once 'connect.php';

class UserFunctions {
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    /**
     * User Authentication
     */
    public function loginUser($studentId, $password) {
        try {
            $stmt = $this->conn->prepare("SELECT id, name, pass, img FROM club_members WHERE id = ?");
            $stmt->bind_param('i', $studentId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();
                if (password_verify($password, $user['pass'])) {
                    session_start();
                    $_SESSION['id'] = $user['id'];
                    $_SESSION['name'] = $user['name'];
                    $_SESSION['img'] = $user['img'];
                    return ['success' => true, 'message' => 'Login successful'];
                }
            }
            return ['success' => false, 'message' => 'Invalid credentials'];
        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Login failed'];
        }
    }
    
    public function logoutUser() {
        session_start();
        session_destroy();
        return ['success' => true, 'message' => 'Logged out successfully'];
    }
    
    /**
     * User Registration
     */
    public function registerUser($data) {
        try {
            // Validate required fields
            $required = ['name', 'studentId', 'batch', 'mail', 'department', 'phone', 'bloodGroup', 'password'];
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    return ['success' => false, 'message' => "Field {$field} is required"];
                }
            }
            
            // Validate email
            if (!filter_var($data['mail'], FILTER_VALIDATE_EMAIL)) {
                return ['success' => false, 'message' => 'Invalid email format'];
            }
            
            if (!str_ends_with($data['mail'], '@rpsu.edu.bd')) {
                return ['success' => false, 'message' => 'Must use student email address'];
            }
            
            // Validate phone
            if (!preg_match('/^\d{11}$/', $data['phone'])) {
                return ['success' => false, 'message' => 'Phone must be 11 digits'];
            }
            
            // Check if user already exists
            $checkStmt = $this->conn->prepare("SELECT id FROM club_members WHERE id = ? OR mail = ?");
            $checkStmt->bind_param('is', $data['studentId'], $data['mail']);
            $checkStmt->execute();
            
            if ($checkStmt->get_result()->num_rows > 0) {
                return ['success' => false, 'message' => 'Student ID or Email already registered'];
            }
            
            // Hash password
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
            
            // Insert user
            $stmt = $this->conn->prepare("
                INSERT INTO club_members (id, img, name, department, batch, mail, phone, pass, bloodGroup) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $defaultImg = 'default.jpg';
            $stmt->bind_param('ississsss', 
                $data['studentId'], $defaultImg, $data['name'], $data['department'], 
                $data['batch'], $data['mail'], $data['phone'], $hashedPassword, $data['bloodGroup']
            );
            
            if ($stmt->execute()) {
                // Add club memberships if provided
                if (!empty($data['clubs']) && is_array($data['clubs'])) {
                    $this->addUserClubs($data['studentId'], $data['clubs']);
                }
                
                return ['success' => true, 'message' => 'Registration successful'];
            }
            
            return ['success' => false, 'message' => 'Registration failed'];
            
        } catch (Exception $e) {
            error_log("Registration error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Registration failed'];
        }
    }
    
    /**
     * User Profile Management
     */
    public function getUserProfile($userId) {
        try {
            $stmt = $this->conn->prepare("
                SELECT id, img, name, department, batch, mail, phone, bloodGroup, created_at 
                FROM club_members WHERE id = ?
            ");
            $stmt->bind_param('i', $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();
                $user['clubs'] = $this->getUserClubs($userId);
                return ['success' => true, 'data' => $user];
            }
            
            return ['success' => false, 'message' => 'User not found'];
        } catch (Exception $e) {
            error_log("Get profile error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to get profile'];
        }
    }
    
    public function updateUserProfile($userId, $data) {
        try {
            $allowedFields = ['name', 'department', 'phone', 'bloodGroup'];
            $updateFields = [];
            $values = [];
            $types = '';
            
            foreach ($allowedFields as $field) {
                if (isset($data[$field]) && !empty($data[$field])) {
                    $updateFields[] = "{$field} = ?";
                    $values[] = $data[$field];
                    $types .= 's';
                }
            }
            
            if (empty($updateFields)) {
                return ['success' => false, 'message' => 'No valid fields to update'];
            }
            
            $values[] = $userId;
            $types .= 'i';
            
            $sql = "UPDATE club_members SET " . implode(', ', $updateFields) . " WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param($types, ...$values);
            
            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Profile updated successfully'];
            }
            
            return ['success' => false, 'message' => 'Update failed'];
        } catch (Exception $e) {
            error_log("Update profile error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Update failed'];
        }
    }
    
    /**
     * Club Management
     */
    public function getUserClubs($userId) {
        try {
            $stmt = $this->conn->prepare("
                SELECT c.id, c.name 
                FROM clubs c 
                JOIN member_clubs mc ON c.id = mc.club_id 
                WHERE mc.member_id = ?
            ");
            $stmt->bind_param('i', $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $clubs = [];
            while ($row = $result->fetch_assoc()) {
                $clubs[] = $row;
            }
            
            return $clubs;
        } catch (Exception $e) {
            error_log("Get user clubs error: " . $e->getMessage());
            return [];
        }
    }
    
    public function addUserClubs($userId, $clubIds) {
        try {
            $stmt = $this->conn->prepare("INSERT INTO member_clubs (member_id, club_id) VALUES (?, ?)");
            
            foreach ($clubIds as $clubId) {
                $clubId = (int)$clubId;
                if ($clubId > 0) {
                    $stmt->bind_param('ii', $userId, $clubId);
                    $stmt->execute();
                }
            }
            
            return ['success' => true, 'message' => 'Clubs added successfully'];
        } catch (Exception $e) {
            error_log("Add user clubs error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to add clubs'];
        }
    }
    
    public function removeUserClub($userId, $clubId) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM member_clubs WHERE member_id = ? AND club_id = ?");
            $stmt->bind_param('ii', $userId, $clubId);
            
            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Club removed successfully'];
            }
            
            return ['success' => false, 'message' => 'Failed to remove club'];
        } catch (Exception $e) {
            error_log("Remove user club error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to remove club'];
        }
    }
    
    /**
     * General Data Retrieval
     */
    public function getAllClubs() {
        try {
            $stmt = $this->conn->prepare("
                SELECT c.id, c.name, c.bgimg, COUNT(mc.member_id) as member_count 
                FROM clubs c 
                LEFT JOIN member_clubs mc ON c.id = mc.club_id 
                GROUP BY c.id, c.name, c.bgimg 
                ORDER BY c.name ASC
            ");
            $stmt->execute();
            $result = $stmt->get_result();
            
            $clubs = [];
            while ($row = $result->fetch_assoc()) {
                $clubs[] = $row;
            }
            
            return ['success' => true, 'data' => $clubs];
        } catch (Exception $e) {
            error_log("Get all clubs error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to get clubs'];
        }
    }
    
    public function getClubMembers($clubId) {
        try {
            $stmt = $this->conn->prepare("
                SELECT cm.id, cm.name, cm.img, cm.department, cm.batch 
                FROM club_members cm 
                JOIN member_clubs mc ON cm.id = mc.member_id 
                WHERE mc.club_id = ? 
                ORDER BY cm.name ASC
            ");
            $stmt->bind_param('i', $clubId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $members = [];
            while ($row = $result->fetch_assoc()) {
                $members[] = $row;
            }
            
            return ['success' => true, 'data' => $members];
        } catch (Exception $e) {
            error_log("Get club members error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to get club members'];
        }
    }
    
    /**
     * Gallery Functions
     */
    public function getGalleryRows() {
        try {
            $stmt = $this->conn->prepare("
                SELECT id, row_header, sub_header, order_num 
                FROM gallery_rows 
                ORDER BY order_num ASC, id ASC
            ");
            $stmt->execute();
            $result = $stmt->get_result();
            
            $rows = [];
            while ($row = $result->fetch_assoc()) {
                $row['images'] = $this->getGalleryImages($row['id']);
                $rows[] = $row;
            }
            
            return ['success' => true, 'data' => $rows];
        } catch (Exception $e) {
            error_log("Get gallery rows error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to get gallery'];
        }
    }
    
    public function getGalleryImages($rowId) {
        try {
            $stmt = $this->conn->prepare("
                SELECT id, image_name, display_order 
                FROM gallery_images 
                WHERE row_id = ? 
                ORDER BY display_order ASC, id ASC
            ");
            $stmt->bind_param('i', $rowId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $images = [];
            while ($row = $result->fetch_assoc()) {
                $images[] = $row;
            }
            
            return $images;
        } catch (Exception $e) {
            error_log("Get gallery images error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Upcoming Events
     */
    public function getUpcomingEvents() {
        try {
            $stmt = $this->conn->prepare("
                SELECT id, heading, content, image, image_side 
                FROM upcomings 
                WHERE is_active = 1 
                ORDER BY id DESC
            ");
            $stmt->execute();
            $result = $stmt->get_result();
            
            $events = [];
            while ($row = $result->fetch_assoc()) {
                $events[] = $row;
            }
            
            return ['success' => true, 'data' => $events];
        } catch (Exception $e) {
            error_log("Get upcoming events error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to get events'];
        }
    }
    
    /**
     * Password Management
     */
    public function changePassword($userId, $currentPassword, $newPassword) {
        try {
            // Verify current password
            $stmt = $this->conn->prepare("SELECT pass FROM club_members WHERE id = ?");
            $stmt->bind_param('i', $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows !== 1) {
                return ['success' => false, 'message' => 'User not found'];
            }
            
            $user = $result->fetch_assoc();
            if (!password_verify($currentPassword, $user['pass'])) {
                return ['success' => false, 'message' => 'Current password is incorrect'];
            }
            
            // Update password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $updateStmt = $this->conn->prepare("UPDATE club_members SET pass = ? WHERE id = ?");
            $updateStmt->bind_param('si', $hashedPassword, $userId);
            
            if ($updateStmt->execute()) {
                return ['success' => true, 'message' => 'Password changed successfully'];
            }
            
            return ['success' => false, 'message' => 'Failed to change password'];
        } catch (Exception $e) {
            error_log("Change password error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to change password'];
        }
    }
}

// Helper function to get user functions instance
function getUserFunctions() {
    global $conn;
    return new UserFunctions($conn);
}
?>