<?php
// Security check - must be logged in as admin
require_once '../funcs/check_admin.php';
include '../funcs/connect.php';

$result = $conn->query("SELECT MAX(id) AS max_row_id FROM gallery_rows");
$row = $result->fetch_assoc();
$histRowId = $row['max_row_id'] ?? 0;

$result = $conn->query("SELECT MAX(id) AS max_img_id FROM gallery_photos");
$row = $result->fetch_assoc();
$histImgId = $row['max_img_id'] ?? 0;

// echo $histRowId;
// echo $histImgId;

$rows_result = $conn->query("
    SELECT id, row_header, sub_header, order_num 
    FROM gallery_rows 
    ORDER BY order_num ASC
");

$gallery_rows = [];
while ($row = $rows_result->fetch_assoc()) {
    $gallery_rows[] = [
        'id'         => (int)$row['id'],
        'header'     => $row['row_header'],
        'sub_header' => $row['sub_header'] ?? '',
        'order_num'  => (int)$row['order_num']
    ];
}

// 2. Get all IMAGES (sorted by row_id and display_order)
$images_result = $conn->query("
    SELECT id, row_id, image_name, display_order 
    FROM gallery_photos 
    ORDER BY row_id, display_order ASC
");
$gallery_images = [];
while ($img = $images_result->fetch_assoc()) {
    $gallery_images[] = [
        'id'            => (int)$img['id'],
        'row_id'        => (int)$img['row_id'],
        'image_name'    => $img['image_name'],
        'display_order' => (int)$img['display_order'],
        'url'           => '../assets/gellary/' . $img['image_name']  // ready to use in <img src="">
    ];


    foreach ($gallery_rows as &$r) {
        if ($r['id'] === (int)$img['row_id']) {
            if (!isset($r['images']) || !is_array($r['images'])) {
                $r['images'] = [];
            }
            $r['images'][] = [
                'id' => (int)$img['id'],
                'url' => '../assets/gellary/' . $img['image_name'],
                'display_order' => (int)$img['display_order']
            ];
            break;
        }
    }
    unset($r);
}

// Optional: Group images by row_id (very useful!)
$images_by_row = [];
foreach ($gallery_images as $img) {
    $images_by_row[$img['row_id']][] = $img;
}

// Now you have everything in clean variables!
/*
   $gallery_rows      → all rows
   $gallery_images    → all images (flat list)
   $images_by_row     → images grouped by row_id (best for display)
*/

// Example: Print to check


// echo "<pre>";
// echo "ROWS:\n";
// print_r($gallery_rows);

// echo "</pre>";

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gallery Admin - Light Mode</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <link href="../assets/css/admidMembers.css" rel="stylesheet">

    <style>
        body {
            background: #e4e4e4ff;
            color: #212529;
            font-family: 'Segoe UI', sans-serif;
        }

        s .row-card {
            background: #a9a9a9ff;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            padding: 24px;
            margin-bottom: 32px;
            border: 1px solid #000000ff;
            transition: all 0.3s ease;
        }

        .row-card:hover {
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
            transform: translateY(-2px);
        }

        .drag-handle {
            cursor: move;
            color: #6c757d;
            font-size: 1.5rem;
        }

        .img-thumb {
            width: clamp(50px, 120px, 390px);
            height: auto;
            object-fit: cover;
            border-radius: 12px;
            border: 3px solid #000000ff;
            transition: all 0.3s ease;
            cursor: move;
            user-select: none;
        }

        .img-thumb:hover {
            transform: scale(1.05);
            border-color: #0d6efd;
            box-shadow: 0 8px 25px rgba(13, 110, 253, 0.25);
        }

        .delete-btn {
            position: absolute;
            top: 8px;
            right: 8px;
            width: clamp(15px, 22px, 30px);
            height: clamp(15px, 22px, 30px);
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 40%;
            font-weight: bold;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .position-relative:hover .delete-btn {
            opacity: 1;
        }

        .sortable-ghost {
            opacity: 0.4;
            background: #cfe7ff;
            border: 2px dashed #0d6efd;
        }

        .sortable-chosen {
            opacity: 0.8;
        }



        .upload-area:hover {
            border-color: #0d6efd;
            background: #e7f3ff;
        }


        .row-img,
        .upload-area {
            border: 1px solid blue;
            border-radius: 20px;
            max-height: 40vh;
            overflow-y: auto;

            scrollbar-width: none;
            /* For Firefox */

            /* For WebKit browsers */
            &::-webkit-scrollbar {
                display: none;
            }

            &:hover {
                scrollbar-width: thin;
                /* For Firefox */

                /* For WebKit browsers */
                &::-webkit-scrollbar {
                    display: block;
                    width: 8px;
                    /* Adjust width as needed */
                }

                &::-webkit-scrollbar-thumb {
                    background: rgba(0, 0, 0, 0.5);
                    /* Adjust thumb color */
                    border-radius: 10px;
                }
            }

            img {
                margin: 2px;
            }
        }

        .upload-area {
            border: 2px dashed #adb5bd;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            background: #f8f9fa;
            transition: all 0.3s;
        }

        .redBord {
            border: 2px solid red !important;
        }
    </style>
</head>

<body class="bg-light">
    <?php
    $current_tab = 'gallery';
    include '../components/admin_t_nav.php'
    ?>

    <div class="container-fluid py-5">
        <div class="text-center mb-5">
            <h1 class="display-5 fw-bold text-primary">
                Gallery Admin Panel
            </h1>
            <p class="text-muted">Drag rows to reorder • Drag images to reorder • Delete with X</p>
        </div>

        <!-- Add New Row Button -->
        <div class="text-center mb-5">
            <button class="btn btn-primary btn-lg px-5 shadow-sm" onclick="addNewRow()">
                Add New Row
            </button>
        </div>

        <!-- Rows Container (Draggable) -->
        <div id="rows-container " class="px-3">
            <?php foreach ($gallery_rows as $e) : ?>
                <div class="row-card position-relative ">

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="drag-handle position-relative p-3">
                            <i class="bi bi-hand-index"></i>
                        </div>
                        <div class="warnDel bg-danger d-none justify-content-center align-items-center px-3 py-1 rounded-2 text-white text-center">Row Title & Image required or this row will be deleted</div>
                        <button class="btn btn-outline-danger btn-sm position-relative m-3"
                            onclick="deleteRow(this, <?php echo $e['id'] ?>)">
                            Delete Row
                        </button>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-7">
                            <input type="text" class="form-control form-control-lg border-0 shadow-sm" name="row_header"
                                value="<?php echo $e['header'] ?>" placeholder="Row Title" data-orginal="<?php echo $e['header'] ?>" onchange="rowchange(this,<?php echo $e['id'] ?>)">
                        </div>
                        <div class="col-md-5">
                            <input type="text" name="sub_header" class="form-control border-0 shadow-sm"
                                value="<?php echo $e['sub_header'] ?>"
                                placeholder="Sub header (optional)"
                                data-orginal="<?php echo $e['sub_header'] ?>"
                                onchange="rowchange(this,<?php echo $e['id'] ?>)">
                        </div>
                    </div>

                    <div class="row g-3 mb-4">

                        <div class="col-md-6 row-img p-3 d-flex flex-wrap align-items-center justify-content-center">

                            <?php
                            $images = $e['images'] ?? [];
                            foreach ($images as $a):
                            ?>
                                <div class=" position-relative">
                                    <img src="<?php echo $a['url'] ?>" class="img-thumb" alt="">
                                    <button class="delete-btn" onclick="deleteImage(this, <?php echo $a['id'] ?>)">X</button>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="col-md-6">
                            <div class="upload-area h-100">
                                <div id="images-<?php echo $e['id'] ?>" class="d-flex flex-wrap gap-2 justify-content-center">
                                </div>
                                <label class="btn btn-outline-primary mt-3">
                                    Upload Images
                                    <input type="file" multiple accept="image/*" class="d-none"
                                        onchange="previewImages(this, 'images-<?php echo $e['id'] ?>')">
                                </label>
                            </div>
                        </div>
                    </div>


                </div>
            <?php endforeach; ?>

            <button class="btn btn-success d-block mx-auto" onclick="saveAll()"> Save All Changes</button>

        </div>
    </div>

    <!-- Warning Modal: Empty Rows -->
    <div class="modal fade" id="warningModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title fw-bold">⚠️ Warning: Empty Rows Detected</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-3">The following rows are missing required data and <strong>will be deleted</strong>:</p>
                    <div id="warningList" class="list-group mb-3">
                        <!-- Dynamically filled -->
                    </div>
                    <div class="alert alert-info">
                        <strong>Do you want to continue?</strong> These rows will be permanently deleted.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-warning" id="confirmEmptyBtn">Continue & Review Changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal: All Changes -->
    <div class="modal fade" id="confirmationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title fw-bold">📋 Review All Changes</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="max-height: 60vh; overflow-y: auto;">
                    <!-- Rows Changes -->
                    <h6 class="fw-bold text-primary mb-2">📝 Row Changes:</h6>
                    <div id="rowChanges" class="mb-4">
                        <!-- Dynamically filled -->
                    </div>

                    <!-- Image Changes -->
                    <h6 class="fw-bold text-success mb-2">🖼️ Image Changes:</h6>
                    <div id="imageChanges" class="mb-4">
                        <!-- Dynamically filled -->
                    </div>

                    <!-- Summary -->
                    <div class="alert alert-primary mt-4">
                        <strong>Summary:</strong>
                        <ul class="mb-0 mt-2">
                            <li id="summaryRows">Rows: 0 created, 0 updated, 0 deleted</li>
                            <li id="summaryImages">Images: 0 uploaded, 0 deleted</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="finalUploadBtn">✓ Save All Changes</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const galleryChanges = {
            rows: {
                created: [], // New rows: { header: "...", sub_header: "...", order_num: X }
                updated_header: [], // 
                updated_subheader: [], // checked
                deleted: [], // checked

            },
            images: {
                uploaded: [], // checked
                deleted: [], // chacked
            }
        };

        const pendingDel = [];

        // new Sortable(document.getElementById('rows-container'), {
        //     animation: 200,
        //     handle: '.drag-handle',
        //     ghostClass: 'sortable-ghost'
        // });


        // document.querySelectorAll('[id^="images-"]').forEach(container => {
        //     new Sortable(container, {
        //         animation: 200,
        //         ghostClass: 'sortable-ghost',
        //         chosenClass: 'sortable-chosen'
        //     });
        // });

        // function addNewRow() {
        //     const container = document.getElementById('rows-container');
        //     const rowId = Date.now(); // unique ID for this row

        //     const newrow = document.createElement('div');
        //     newrow.className = 'row-card position-relative';
        //     newrow.innerHTML = `
        //                     <div class="d-flex justify-content-between align-items-center mb-4">
        //                         <div class="drag-handle position-relative p-3">
        //                             <i class="bi bi-hand-index"></i>
        //                         </div>
        //                         <button class="btn btn-outline-danger btn-sm position-relative m-3" 
        //                                 onclick="this.closest('.row-card').remove()">
        //                             Delete Row
        //                         </button>
        //                     </div>

        //                     <div class="row g-3 mb-4">
        //                         <div class="col-md-7">
        //                             <input type="text" class="form-control form-control-lg border-0 shadow-sm" 
        //                                 value="New Row" placeholder="Row Title">
        //                         </div>
        //                         <div class="col-md-5">
        //                             <input type="text" class="form-control border-0 shadow-sm" 
        //                                 placeholder="Sub header (optional)">
        //                         </div>
        //                     </div>

        //                     <!-- Images Container (Draggable) -->
        //                     <div class="upload-area mb-3">
        //                         <div id="images-${rowId}" class="d-flex flex-wrap gap-3 justify-content-center p-3">

        //                         </div>

        //                         <label class="btn btn-outline-primary mt-3">
        //                             Upload Images
        //                             <input type="file" multiple accept="image/*" class="d-none" 
        //                                 onchange="previewImages(this, 'images-${rowId}')">
        //                         </label>
        //                     </div>
        //                 `;

        //     container.appendChild(newrow);

        // Enable drag-to-reorder for the new images container
        // new Sortable(document.getElementById(`images-${rowId}`), {
        //     animation: 200,
        //     ghostClass: 'sortable-ghost',
        //     chosenClass: 'sortable-chosen'
        // });
        // }

        let hirowId = parseInt(<?php echo (int) json_decode($histRowId) ?>);

        function addNewRow() {

            hirowId++;
            console.log(hirowId)
            const newRow = document.createElement('div');
            newRow.className = 'row-card position-relative new_row';
            newRow.innerHTML = `
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="drag-handle position-relative p-3">
                <i class="bi bi-hand-index"></i>
            </div>
              <div class="warnDel bg-danger d-none justify-content-center align-items-center px-3 py-1 rounded-2 text-white text-center">Row Title & Image required or this row will be deleted</div>

            <button class="btn btn-outline-danger btn-sm position-relative m-3 new_row"
                onclick="deleteRow(this, ${hirowId})">
                Delete Row
            </button>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-7">
                <input type="text" class="form-control form-control-lg border-0 shadow-sm new_row" name="row_header"
                    value="" placeholder="Row Title" required  onchange="rowchange(this,${hirowId})">
            </div>
            <div class="col-md-5">
                <input type="text" name="sub_header" class="form-control border-0 shadow-sm"
                    value="" placeholder="Sub header (optional)"
                     onchange="rowchange(this,${hirowId})">
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-6 row-img p-3 d-flex flex-wrap align-items-center justify-content-center">
                <!-- existing images would go here (none for new row) -->
            </div>

            <div class="col-md-6">
                            <div class="upload-area h-100">
                                <div id="images-${hirowId}" class="d-flex flex-wrap gap-2 justify-content-center">
                                </div>
                                <label class="btn btn-outline-primary mt-3">
                                    Upload Images
                                    <input type="file" multiple accept="image/*" class="d-none"
                                        onchange="previewImages(this, 'images-${hirowId}')">
                                </label>
                            </div>
                        </div>
        </div>
    `;

            const firstRow = document.querySelector('.row-card');
            if (firstRow && firstRow.parentElement) {
                firstRow.parentElement.insertBefore(newRow, firstRow);
            } else {
                // If no rows exist yet, just append normally
                document.body.appendChild(newRow); // or your main container
            }
        }

        function previewImages(input, containerId) {
            const container = document.getElementById(containerId);

            Array.from(input.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'image-item position-relative';

                    const img = document.createElement('img');
                    img.src = e.target.result; // blob URL (for preview)
                    img.className = 'img-thumb';

                    // Store the filename and file object for later upload
                    img.dataset.filename = file.name;
                    img.dataset.fileSize = file.size;
                    img.dataset.fileType = file.type;

                    // Store the actual File object as a property (not in data attribute)
                    img.fileObject = file;

                    div.appendChild(img);

                    // Delete button
                    const delBtn = document.createElement('button');
                    delBtn.className = 'delete-btn';
                    delBtn.innerHTML = 'X';
                    delBtn.onclick = () => div.remove();
                    div.appendChild(delBtn);

                    container.appendChild(div);
                };
                reader.readAsDataURL(file);
            });
        }

        function deleteImage(btn, img_id) {
            checkIfAnyEmty();
            galleryChanges.images.deleted.push(img_id);
            console.log(galleryChanges.images.deleted);
            btn.parentElement.remove();

        }

        function deleteRow(btn, row_id) {
            const checkNew = btn.classList.contains('new_row');
            if (!checkNew) {
                galleryChanges.rows.deleted.push(row_id);
                console.log(galleryChanges.rows.deleted);
            }
            console.log(checkNew)
            btn.parentElement.parentElement.remove();

        }

        function rowchange(e, rowid) {
            console.log(e.name);
            console.log(rowid);
            console.log(e.value);
            if (e.classList.contains('new_row')) {
                if (e.value == '' || e.value == null) {

                    e.classList.add('redBord');
                    e.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                } else {
                    e.classList.remove('redBord');
                }
            }

            if (e.dataset.orginal != e.value) {
                console.log(e.value);
                if (e.name === "row_header") {
                    galleryChanges.rows.updated_header = galleryChanges.rows.updated_header
                        .filter(item => item.rowid != rowid);

                    galleryChanges.rows.updated_header.push({
                        rowid: rowid,
                        value: e.value
                    });
                } else if (e.name === "sub_header") {
                    galleryChanges.rows.updated_subheader = galleryChanges.rows.updated_subheader
                        .filter(item => item.rowid != rowid);
                    galleryChanges.rows.updated_subheader.push({
                        rowid: rowid,
                        value: e.value
                    });
                }
            }
        }

        function checkIfAnyEmty() {
            const dbPhotos = document.querySelectorAll('.row-img');
            let anyEmpty = false;
            pendingDel.length = 0; // Clear pendingDel array

            dbPhotos.forEach(container => {
                const rowCard = container.closest('.row-card');
                const warn = rowCard?.querySelector('.warnDel');

                // Check BOTH left side (existing from DB) and right side (newly uploaded)
                const hasImgLeft = container.querySelector('img') !== null;
                const uploadContainer = rowCard?.querySelector('[id^="images-"]');
                const hasImgRight = uploadContainer ? uploadContainer.querySelector('img') !== null : false;

                // Get row id
                let rowId = null;
                if (uploadContainer) {
                    rowId = uploadContainer.id.replace('images-', '');
                } else {
                    const delBtn = rowCard?.querySelector('button[onclick*="deleteRow"]');
                    if (delBtn) {
                        const m = delBtn.getAttribute('onclick').match(/deleteRow\([^,]+,\s*(\d+)\)/);
                        if (m) rowId = m[1];
                    }
                }

                // WARN ONLY IF NO IMAGES AT ALL (both left and right empty)
                if (!hasImgLeft && !hasImgRight) {
                    anyEmpty = true;
                    if (warn) warn.classList.remove('d-none');
                    warn?.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });

                    if (rowId !== null) {
                        const idNum = parseInt(rowId, 10);
                        if (!Number.isNaN(idNum) && !pendingDel.includes(idNum)) {
                            pendingDel.push(idNum);
                        }
                    }
                } else {
                    if (warn) warn.classList.add('d-none');

                    if (rowId !== null) {
                        const idNum = parseInt(rowId, 10);
                        const idx = pendingDel.indexOf(idNum);
                        if (idx !== -1) pendingDel.splice(idx, 1);
                    }
                }
            });

            return anyEmpty;
        }

        function saveAll() {
            let emptyRows = checkIfAnyEmty(); // Returns true if empty rows exist

            if (emptyRows) {
                // Show warning modal with details
                showWarningModal();
            } else {
                // No empty rows; go straight to confirmation
                showConfirmationModal();
            }
        }

        function showWarningModal() {
            const warningList = document.getElementById('warningList');
            warningList.innerHTML = ''; // Clear

            // Build list of empty rows with details
            const dbPhotos = document.querySelectorAll('.row-card');
            dbPhotos.forEach(rowCard => {
                const rowImgContainer = rowCard.querySelector('.row-img');
                const uploadContainer = rowCard.querySelector('[id^="images-"]');
                const headerInput = rowCard.querySelector('input[name="row_header"]');

                const hasImgLeft = rowImgContainer.querySelector('img') !== null;
                const hasImgRight = uploadContainer ? uploadContainer.querySelector('img') !== null : false;
                const headerEmpty = !headerInput || !headerInput.value.trim();

                // If missing header OR no images, it's empty
                if (headerEmpty || (!hasImgLeft && !hasImgRight)) {
                    const rowId = uploadContainer ? uploadContainer.id.replace('images-', '') : 'unknown';
                    const headerText = headerInput?.value.trim() || '(No title)';

                    const item = document.createElement('div');
                    item.className = 'list-group-item list-group-item-danger';
                    item.innerHTML = `
                        <div class="d-flex justify-content-between">
                            <div>
                                <strong>Row ID: ${rowId}</strong>
                                <br><small class="text-muted">Title: <em>${headerText}</em></small>
                            </div>
                            <div class="text-end">
                                ${headerEmpty ? '<span class="badge bg-danger">Missing Title</span> ' : ''}
                                ${!hasImgLeft && !hasImgRight ? '<span class="badge bg-danger">No Images</span>' : ''}
                            </div>
                        </div>
                    `;
                    warningList.appendChild(item);
                }
            });

            // Wire up the "Continue" button
            document.getElementById('confirmEmptyBtn').onclick = () => {
                // Close warning modal
                bootstrap.Modal.getInstance(document.getElementById('warningModal')).hide();
                // Show confirmation modal
                setTimeout(showConfirmationModal, 500);
            };

            // Show modal
            new bootstrap.Modal(document.getElementById('warningModal')).show();
        }

        function showConfirmationModal() {
            // Rebuild galleryChanges based on current DOM state
            rebuildGalleryChanges();

            // Populate the confirmation modal with detailed info
            const rowChangesDiv = document.getElementById('rowChanges');
            const imageChangesDiv = document.getElementById('imageChanges');

            rowChangesDiv.innerHTML = '';
            imageChangesDiv.innerHTML = '';

            // Row changes
            if (galleryChanges.rows.created.length > 0) {
                const createdList = document.createElement('div');
                createdList.className = 'alert alert-success';
                createdList.innerHTML = '<strong>✓ Created:</strong><ul class="mb-0 mt-2">';
                galleryChanges.rows.created.forEach(row => {
                    createdList.innerHTML += `<li>Title: <strong>${row.header}</strong> ${row.sub_header ? `(${row.sub_header})` : ''}</li>`;
                });
                createdList.innerHTML += '</ul>';
                rowChangesDiv.appendChild(createdList);
            }

            if (galleryChanges.rows.updated_header.length > 0 || galleryChanges.rows.updated_subheader.length > 0) {
                const updatedList = document.createElement('div');
                updatedList.className = 'alert alert-info';
                updatedList.innerHTML = '<strong>✏️ Updated:</strong><ul class="mb-0 mt-2">';

                const allUpdates = new Set([
                    ...galleryChanges.rows.updated_header.map(u => u.rowid),
                    ...galleryChanges.rows.updated_subheader.map(u => u.rowid)
                ]);
                allUpdates.forEach(rowId => {
                    updatedList.innerHTML += `<li>Row ID: <strong>${rowId}</strong></li>`;
                });
                updatedList.innerHTML += '</ul>';
                rowChangesDiv.appendChild(updatedList);
            }

            if (galleryChanges.rows.deleted.length > 0) {
                const deletedList = document.createElement('div');
                deletedList.className = 'alert alert-danger';
                deletedList.innerHTML = '<strong>🗑️ Deleted:</strong><ul class="mb-0 mt-2">';
                galleryChanges.rows.deleted.forEach(id => {
                    deletedList.innerHTML += `<li>Row ID: <strong>${id}</strong></li>`;
                });
                deletedList.innerHTML += '</ul>';
                rowChangesDiv.appendChild(deletedList);
            }

            if (rowChangesDiv.innerHTML === '') {
                rowChangesDiv.innerHTML = '<p class="text-muted">No row changes.</p>';
            }

            // Image changes
            if (galleryChanges.images.uploaded.length > 0) {
                const uploadedList = document.createElement('div');
                uploadedList.className = 'alert alert-success';
                uploadedList.innerHTML = '<strong>✓ Uploaded:</strong><ul class="mb-0 mt-2">';
                galleryChanges.images.uploaded.forEach(img => {
                    uploadedList.innerHTML += `<li>Row ${img.row_id}: <strong>${img.name}</strong></li>`;
                });
                uploadedList.innerHTML += '</ul>';
                imageChangesDiv.appendChild(uploadedList);
            }

            if (galleryChanges.images.deleted.length > 0) {
                const deletedImgList = document.createElement('div');
                deletedImgList.className = 'alert alert-danger';
                deletedImgList.innerHTML = '<strong>🗑️ Deleted:</strong><ul class="mb-0 mt-2">';
                galleryChanges.images.deleted.forEach(id => {
                    deletedImgList.innerHTML += `<li>Image ID: <strong>${id}</strong></li>`;
                });
                deletedImgList.innerHTML += '</ul>';
                imageChangesDiv.appendChild(deletedImgList);
            }

            if (imageChangesDiv.innerHTML === '') {
                imageChangesDiv.innerHTML = '<p class="text-muted">No image changes.</p>';
            }

            // Update summary
            document.getElementById('summaryRows').textContent =
                `Rows: ${galleryChanges.rows.created.length} created, ` +
                `${new Set([...galleryChanges.rows.updated_header.map(u => u.rowid), ...galleryChanges.rows.updated_subheader.map(u => u.rowid)]).size} updated, ` +
                `${galleryChanges.rows.deleted.length} deleted`;
            document.getElementById('summaryImages').textContent =
                `Images: ${galleryChanges.images.uploaded.length} uploaded, ${galleryChanges.images.deleted.length} deleted`;

            // Wire up the "Save All Changes" button
            document.getElementById('finalUploadBtn').onclick = uploadAllChanges;

            // Show modal
            new bootstrap.Modal(document.getElementById('confirmationModal')).show();
        }

        function rebuildGalleryChanges() {
            // Rebuild the entire galleryChanges object from current DOM state

            // Reset to clean state
            galleryChanges.rows.created = [];
            galleryChanges.rows.updated_header = [];
            galleryChanges.rows.updated_subheader = [];
            galleryChanges.images.uploaded = [];
            // Note: deleted rows/images are already tracked in galleryChanges

            let hiImgId = parseInt(<?php echo (int) json_decode($histImgId) ?>);

            // Iterate through all rows
            document.querySelectorAll('.row-card').forEach(rowCard => {
                const uploadContainer = rowCard.querySelector('[id^="images-"]');
                const headerInput = rowCard.querySelector('input[name="row_header"]');
                const subheaderInput = rowCard.querySelector('input[name="sub_header"]');

                if (!uploadContainer || !headerInput) return;

                const rowId = parseInt(uploadContainer.id.replace('images-', ''));
                const header = headerInput.value.trim();
                const subheader = subheaderInput?.value.trim() || '';

                // Check if this is a new row (has 'new_row' class)
                const isNewRow = rowCard.classList.contains('new_row');

                if (isNewRow && header) {
                    // Add to created rows (only if it has a title)
                    galleryChanges.rows.created.push({
                        id: rowId,
                        header: header,
                        sub_header: subheader,
                        order_num: 0
                    });
                }

                // Collect images from the upload container
                const images = uploadContainer.querySelectorAll('img');
                images.forEach(img => {
                    const filename = img.dataset.filename || img.src.split('/').pop().split('?')[0];

                    // Only add if it's from the upload area (has data-filename or is in upload container)
                    if (img.dataset.filename || uploadContainer.contains(img)) {
                        hiImgId++;
                        galleryChanges.images.uploaded.push({
                            row_id: rowId,
                            id: hiImgId,
                            name: filename,
                            file: img.dataset.file || null // Will be populated with actual file if available
                        });
                    }
                });
            });

            console.log('Rebuilt galleryChanges:', galleryChanges);
        }

        function uploadAllChanges() {
            console.log('Starting two-step upload process...');

            // STEP 1: Upload files to disk
            const fileFormData = new FormData();

            // Collect all file objects from upload containers
            const fileMap = {};
            document.querySelectorAll('[id^="images-"]').forEach(container => {
                const images = container.querySelectorAll('img');
                images.forEach(img => {
                    if (img.fileObject) {
                        fileMap[img.fileObject.name] = img.fileObject;
                    }
                });
            });

            // Prepare meta mapping to tell server which original filename belongs to which row
            const meta = galleryChanges.images.uploaded.map(i => ({
                original: i.name,
                row_id: i.row_id,
                id: i.id
            }));
            if (meta.length > 0) fileFormData.append('meta', JSON.stringify(meta));

            // Append all files to FormData for upload (use files[] so PHP receives arrays)
            Object.entries(fileMap).forEach(([name, file]) => {
                fileFormData.append('files[]', file);
            });

            // Check if there are any files to upload
            if (Object.keys(fileMap).length === 0) {
                console.log('No files to upload, proceeding to save changes...');
                performSaveChanges();
                return;
            }

            // Upload files first
            fetch('../funcs/upload_gallery_images.php', {
                    method: 'POST',
                    body: fileFormData
                })
                .then(r => r.json())
                .then(uploadResponse => {
                    console.log('Upload response:', uploadResponse);
                    if (uploadResponse && uploadResponse.successes) {
                        console.log('Files uploaded successfully, now saving changes...');
                        performSaveChanges();
                    } else {
                        alert('✗ File upload failed: ' + (uploadResponse.error || 'Unknown error'));
                        console.log('Upload errors:', uploadResponse.errors);
                    }
                })
                .catch(err => {
                    console.error('File upload error:', err);
                    alert('✗ File upload failed: ' + err.message);
                });

            // STEP 2: Save database changes (rows and image metadata)
            function performSaveChanges() {
                const changesFormData = new FormData();
                changesFormData.append('changes', JSON.stringify({
                    rows: galleryChanges.rows,
                    images: {
                        uploaded: galleryChanges.images.uploaded.map(img => ({
                            row_id: img.row_id,
                            id: img.id,
                            name: img.name
                        })),
                        deleted: galleryChanges.images.deleted
                    }
                }));

                fetch('../funcs/save_gallery_changes.php', {
                        method: 'POST',
                        body: changesFormData
                    })
                    .then(r => r.json())
                    .then(saveResponse => {
                        console.log('Save response:', saveResponse);
                        if (saveResponse && saveResponse.success) {
                            alert('✓ All changes saved successfully!');
                            window.location.reload();
                        } else {
                            alert('✗ Save failed: ' + (saveResponse.error || 'Unknown error'));
                            console.log('Save errors:', saveResponse.errors);
                        }
                    })
                    .catch(err => {
                        console.error('Save error:', err);
                        alert('✗ Save failed: ' + err.message);
                    });
            }
        }
    </script>
</body>

</html>