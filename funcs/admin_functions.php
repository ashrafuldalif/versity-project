<?php
/**
 * Admin Functions - All CRUD operations for administrators
 * Uses colors from root.css variables
 */

require_once 'connect.php';

class AdminFunctions {
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    /**
     * Admin Authentication
     */
    public function loginAdmin($username, $password) {
        try {
            $stmt = $this->conn->prepare("SELECT id, username, password FROM admins WHERE username = ?");
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 1) {
                $admin = $result->fetch_assoc();
                if (password_verify($password, $admin['password'])) {
                    session_start();
                    $_SESSION['admin_id'] = $admin['id'];
                    $_SESSION['admin_username'] = $admin['username'];
                    return ['success' => true, 'message' => 'Admin login successful'];
                }
            }
            return ['success' => false, 'message' => 'Invalid admin credentials'];
        } catch (Exception $e) {
            error_log("Admin login error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Admin login failed'];
        }
    }
    
    /**
     * Member Management
     */
    public function getAllMembers($filters = []) {
        try {
            $sql = "
                SELECT cm.id, cm.img, cm.name, cm.department, cm.batch, 
                       cm.mail, cm.phone, cm.bloodGroup, cm.created_at
                FROM club_members cm
                WHERE 1=1
            ";
            
            $params = [];
            $types = '';
            
            // Apply filters
            if (!empty($filters['department'])) {
                $sql .= " AND cm.department = ?";
                $params[] = $filters['department'];
                $types .= 's';
            }
            
            if (!empty($filters['batch'])) {
                $sql .= " AND cm.batch = ?";
                $params[] = $filters['batch'];
                $types .= 'i';
            }
            
            if (!empty($filters['bloodGroup'])) {
                $sql .= " AND cm.bloodGroup = ?";
                $params[] = $filters['bloodGroup'];
                $types .= 's';
            }
            
            $sql .= " ORDER BY cm.name ASC";
            
            $stmt = $this->conn->prepare($sql);
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            
            $members = [];
            while ($row = $result->fetch_assoc()) {
                $row['clubs'] = $this->getMemberClubs($row['id']);
                $members[] = $row;
            }
            
            return ['success' => true, 'data' => $members];
        } catch (Exception $e) {
            error_log("Get all members error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to get members'];
        }
    }
    
    public function getMemberClubs($memberId) {
        try {
            $stmt = $this->conn->prepare("
                SELECT c.id, c.name 
                FROM clubs c 
                JOIN member_clubs mc ON c.id = mc.club_id 
                WHERE mc.member_id = ?
            ");
            $stmt->bind_param('i', $memberId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $clubs = [];
            while ($row = $result->fetch_assoc()) {
                $clubs[] = $row;
            }
            
            return $clubs;
        } catch (Exception $e) {
            error_log("Get member clubs error: " . $e->getMessage());
            return [];
        }
    }
    
    public function searchMembers($field, $term) {
        try {
            $allowedFields = ['name', 'id', 'mail'];
            if (!in_array($field, $allowedFields)) {
                return ['success' => false, 'message' => 'Invalid search field'];
            }
            
            $sql = "
                SELECT cm.id, cm.img, cm.name, cm.department, cm.batch, 
                       cm.mail, cm.phone, cm.bloodGroup
                FROM club_members cm
                WHERE cm.{$field} LIKE ?
                ORDER BY cm.{$field} ASC
                LIMIT 20
            ";
            
            $searchTerm = "%{$term}%";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('s', $searchTerm);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $members = [];
            while ($row = $result->fetch_assoc()) {
                $row['clubs'] = $this->getMemberClubs($row['id']);
                $members[] = $row;
            }
            
            return ['success' => true, 'data' => $members];
        } catch (Exception $e) {
            error_log("Search members error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Search failed'];
        }
    }
    
    /**
     * Club Management
     */
    public function createClub($name, $bgimg = null) {
        try {
            $stmt = $this->conn->prepare("INSERT INTO clubs (name, bgimg) VALUES (?, ?)");
            $stmt->bind_param('ss', $name, $bgimg);
            
            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Club created successfully', 'id' => $this->conn->insert_id];
            }
            
            return ['success' => false, 'message' => 'Failed to create club'];
        } catch (Exception $e) {
            error_log("Create club error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to create club'];
        }
    }
    
    public function updateClub($clubId, $name, $bgimg = null) {
        try {
            if ($bgimg) {
                $stmt = $this->conn->prepare("UPDATE clubs SET name = ?, bgimg = ? WHERE id = ?");
                $stmt->bind_param('ssi', $name, $bgimg, $clubId);
            } else {
                $stmt = $this->conn->prepare("UPDATE clubs SET name = ? WHERE id = ?");
                $stmt->bind_param('si', $name, $clubId);
            }
            
            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Club updated successfully'];
            }
            
            return ['success' => false, 'message' => 'Failed to update club'];
        } catch (Exception $e) {
            error_log("Update club error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to update club'];
        }
    }
    
    /**
     * Gallery Management
     */
    public function createGalleryRow($header, $subHeader = null, $orderNum = 0) {
        try {
            $stmt = $this->conn->prepare("INSERT INTO gallery_rows (row_header, sub_header, order_num) VALUES (?, ?, ?)");
            $stmt->bind_param('ssi', $header, $subHeader, $orderNum);
            
            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Gallery row created successfully', 'id' => $this->conn->insert_id];
            }
            
            return ['success' => false, 'message' => 'Failed to create gallery row'];
        } catch (Exception $e) {
            error_log("Create gallery row error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to create gallery row'];
        }
    }
    
    public function addGalleryImages($rowId, $imageNames) {
        try {
            $stmt = $this->conn->prepare("INSERT INTO gallery_images (row_id, image_name, display_order) VALUES (?, ?, ?)");
            
            foreach ($imageNames as $index => $imageName) {
                $stmt->bind_param('isi', $rowId, $imageName, $index);
                $stmt->execute();
            }
            
            return ['success' => true, 'message' => 'Images added successfully'];
        } catch (Exception $e) {
            error_log("Add gallery images error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to add images'];
        }
    }
    
    /**
     * Statistics
     */
    public function getDashboardStats() {
        try {
            $stats = [];
            
            // Total members
            $result = $this->conn->query("SELECT COUNT(*) as count FROM club_members");
            $stats['total_members'] = $result->fetch_assoc()['count'];
            
            // Total clubs
            $result = $this->conn->query("SELECT COUNT(*) as count FROM clubs");
            $stats['total_clubs'] = $result->fetch_assoc()['count'];
            
            // Active executives
            $result = $this->conn->query("SELECT COUNT(*) as count FROM executives WHERE approved = 1 AND active = 1");
            $stats['active_executives'] = $result->fetch_assoc()['count'];
            
            // Recent registrations (last 30 days)
            $result = $this->conn->query("SELECT COUNT(*) as count FROM club_members WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
            $stats['recent_registrations'] = $result->fetch_assoc()['count'];
            
            return ['success' => true, 'data' => $stats];
        } catch (Exception $e) {
            error_log("Get dashboard stats error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to get statistics'];
        }
    }
}

// Helper function to get admin functions instance
function getAdminFunctions() {
    global $conn;
    return new AdminFunctions($conn);
}
?>