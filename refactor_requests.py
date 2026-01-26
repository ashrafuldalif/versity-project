#!/usr/bin/env python3
"""Refactor requests.php to use admin_functions.php"""

with open('admin/requests.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Add admin_functions.php require and session_start
content = content.replace(
    "include __DIR__ . '/../funcs/connect.php';",
    "include __DIR__ . '/../funcs/connect.php';\nrequire_once __DIR__ . '/../funcs/admin_functions.php';\n\nsession_start();"
)

# Replace the toggle approve section with function
content = content.replace(
    '''if (isset($_GET['toggle_approve'])) {
    $id = (int)$_GET['toggle_approve'];
    $conn->query("UPDATE executives SET approved = NOT approved WHERE id = $id");
    // After approving, send admin to the executives list so approved entries are visible there
    header("Location: executives.php");
    exit;
}''',
    '''if (isset($_GET['toggle_approve'])) {
    toggleExecutiveApproval($conn, $_GET['toggle_approve']);
    redirectWithMessage('executives.php', 'Executive approval status updated!');
}''')

# Replace delete request section
content = content.replace(
    '''if (isset($_GET['delete_id'])) {
    $did = (int)$_GET['delete_id'];
    $del = $conn->prepare("DELETE FROM executives WHERE id = ?");
    if ($del) {
        $del->bind_param('i', $did);
        $del->execute();
        $del->close();
    }
    header("Location: requests.php");
    exit;
}''',
    '''if (isset($_GET['delete_id'])) {
    deleteExecutive($conn, $_GET['delete_id']);
    redirectWithMessage('requests.php', 'Executive request deleted successfully!');
}''')

# Replace the fetch executives section - need to get pending executives
content = content.replace(
    '''/* ------------------------------
   FETCH EXECUTIVES
--------------------------------*/
$sql = "
SELECT e.id, e.name, e.email, e.approved,
       c.name AS club_name,
       p.position_name
FROM executives e
LEFT JOIN clubs c ON e.club_id = c.id
LEFT JOIN positions p ON e.position_id = p.id
WHERE e.approved = 0
ORDER BY e.id DESC
";

$executives = $conn->query($sql);''',
    '''/* ------------------------------
   FETCH PENDING EXECUTIVES
--------------------------------*/
$executives_result = getPendingExecutives($conn);
$executives = new ArrayIterator($executives_result);''')

# Fix the rendering to use the new array structure
content = content.replace(
    '''                    <tbody>
                        <?php while ($row = $executives->fetch_assoc()): ?>''',
    '''                    <tbody>
                        <?php foreach ($executives_result as $row): ?>''')

content = content.replace(
    '''                        <?php endwhile; ?>''',
    '''                        <?php endforeach; ?>''')

# Fix the empty check
content = content.replace(
    '''        <?php if ($executives->num_rows == 0): ?>
            <div class="alert alert-info mt-4">No executives found.</div>
        <?php endif; ?>''',
    '''        <?php if (empty($executives_result)): ?>
            <div class="alert alert-info mt-4">No pending executive requests found.</div>
        <?php endif; ?>''')

with open('admin/requests.php', 'w', encoding='utf-8') as f:
    f.write(content)

print("✓ Successfully refactored requests.php to use admin_functions.php")
