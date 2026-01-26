#!/usr/bin/env python3
"""Refactor executives.php to use admin_functions.php"""

with open('admin/executives.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Add admin_functions.php require and session_start
content = content.replace(
    "include \"../funcs/connect.php\";",
    "include \"../funcs/connect.php\";\nrequire_once \"../funcs/admin_functions.php\";\n\nsession_start();"
)

# Replace position fetching with function
content = content.replace(
    '''// Fetch positions for position filter dropdown
$positions = [];
$posStmt = $conn->prepare("SELECT id, position_name FROM positions ORDER BY id ASC");
if ($posStmt) {
    $posStmt->execute();
    $posRes = $posStmt->get_result();
    while ($pr = $posRes->fetch_assoc()) {
        $positions[] = $pr;
    }
    $posStmt->close();
}''',
    '''// Fetch positions for position filter dropdown
$positions = getPositions($conn);''')

# Replace the big data fetching and rendering section
# First, find and replace the SQL query and data processing
old_exec_fetch = '''            $sql = "SELECT e.id, e.name, e.batch, e.email, e.img, e.blood_group, e.department, e.phone, e.club_id, e.active, p.position_name 
                    FROM executives e 
                    LEFT JOIN positions p ON e.position_id = p.id 
                    WHERE e.approved = 1
                    ORDER BY COALESCE(p.id, 999), e.name ASC
                    ";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->get_result();
            $i = 1;
            $storage = [];
            while ($row = $result->fetch_assoc()) {

                $name = $row['name'];
                $id = $row['id'];
                $batch = $row['batch'];
                $email = $row['email'];
                $imgl = $row['img'];
                $imgl = $imgl ? "../assets/members/" . $imgl : "../assets/members/default.jpg";
                $row['img'] = $imgl;
                $bgroup = $row['blood_group'];
                $phone = $row['phone'];
                $department = $row['department'];
                $position = $row['position_name'] ?? 'N/A';
                $active = isset($row['active']) ? (int)$row['active'] : 0;
                $club_id = $row['club_id'];

                // Fetch club name if club_id exists
                $clubName = '';
                if ($club_id) {
                    $clubSql = "SELECT name FROM clubs WHERE id = ?";
                    $clubStmt = $conn->prepare($clubSql);
                    $clubStmt->bind_param('i', $club_id);
                    $clubStmt->execute();
                    $clubResult = $clubStmt->get_result();
                    if ($clubRow = $clubResult->fetch_assoc()) {
                        $clubName = $clubRow['name'];
                    }
                    $clubStmt->close();
                }

                $row['club'] = $clubName;
                $row['mail'] = $email; // for search compatibility
                $row['bloodGroup'] = $bgroup; // for filter compatibility
                $row['active'] = $active;
                $storage[] = $row;

                // server-side render initial row (kept for progressive enhancement)
                $btnLabel = $active ? 'Deactivate' : 'Activate';
                $btnClass = $active ? 'btn-danger' : 'btn-success';
                echo "
                              <tr>            
                              <td>$i</td>
                        <td class=\"d-flex justify-content-center\"><img src=\"${imgl}\" class=\"img-thumbnail cprofile \" ></td>
                        <td>$id</td>
                        <td>$name</td>
                        <td>$position</td>
                        <td>$batch</td>
                        <td>$department</td>
                        <td>$clubName</td>
                        <td>$email</td>
                        <td>$phone</td>
                        <td><button data-id=\"$id\" data-active=\"$active\" class=\"btn btn-sm $btnClass btn-toggle-active\">$btnLabel</button></td>
                        <td>$bgroup</td>
                    </tr>
                        ";
                $i++;
            }

            $stmt->close();
            $conn->close();'''

new_exec_fetch = '''            // Fetch approved executives
            $executives = getApprovedExecutives($conn);
            $storage = [];
            $i = 1;
            
            foreach ($executives as $row) {
                $name = $row['name'];
                $id = $row['id'];
                $batch = $row['batch'];
                $email = $row['email'];
                $imgl = $row['img'];
                $imgl = $imgl ? "../assets/members/" . $imgl : "../assets/members/default.jpg";
                $row['img'] = $imgl;
                $bgroup = $row['blood_group'];
                $phone = $row['phone'];
                $department = $row['department'];
                $position = $row['position_name'] ?? 'N/A';
                $active = isset($row['active']) ? (int)$row['active'] : 0;
                $clubName = $row['club_name'] ?? '';

                $row['club'] = $clubName;
                $row['mail'] = $email;
                $row['bloodGroup'] = $bgroup;
                $row['active'] = $active;
                $storage[] = $row;

                // server-side render initial row
                $btnLabel = $active ? 'Deactivate' : 'Activate';
                $btnClass = $active ? 'btn-danger' : 'btn-success';
                echo "
                              <tr>            
                              <td>$i</td>
                        <td class=\"d-flex justify-content-center\"><img src=\"${imgl}\" class=\"img-thumbnail cprofile \" ></td>
                        <td>$id</td>
                        <td>$name</td>
                        <td>$position</td>
                        <td>$batch</td>
                        <td>$department</td>
                        <td>$clubName</td>
                        <td>$email</td>
                        <td>$phone</td>
                        <td><button data-id=\"$id\" data-active=\"$active\" class=\"btn btn-sm $btnClass btn-toggle-active\">$btnLabel</button></td>
                        <td>$bgroup</td>
                    </tr>
                        ";
                $i++;
            }'''

content = content.replace(old_exec_fetch, new_exec_fetch)

with open('admin/executives.php', 'w', encoding='utf-8') as f:
    f.write(content)

print("✓ Successfully refactored executives.php to use admin_functions.php")
