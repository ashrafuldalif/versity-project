<?php
session_start();
include 'funcs/connect.php';

// Fetch gallery rows and images
$rowsQuery = "SELECT id, row_header, sub_header, order_num 
              FROM gallery_rows 
              ORDER BY order_num ASC";
$rowsResult = $conn->query($rowsQuery);
$galleryRows = [];

while ($row = $rowsResult->fetch_assoc()) {
    $rowId = $row['id'];

    // Fetch images for this row
    $imagesQuery = "SELECT id, image_name, display_order 
                    FROM gallery_photos 
                    WHERE row_id = ? 
                    ORDER BY display_order ASC";
    $imgStmt = $conn->prepare($imagesQuery);
    $imgStmt->bind_param('i', $rowId);
    $imgStmt->execute();
    $imagesResult = $imgStmt->get_result();

    $images = [];
    while ($img = $imagesResult->fetch_assoc()) {
        $images[] = $img;
    }

    $row['images'] = $images;
    $galleryRows[] = $row;
    $imgStmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gallery - RPSU SWC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/root.css">
    <link rel="stylesheet" href="assets/css/gellary.css">
    <link rel="stylesheet" href="assets/css/nav.css">

</head>

<body>
    <?php include 'components/navbar.php'; ?>

    <main style="margin-top: 5rem; padding-top: 1rem;">

        <div class="container-fluid py-4">
            <?php if (!empty($galleryRows)): ?>
                <?php foreach ($galleryRows as $section): ?>
                    <div class="slider-section">
                        <h3><?php echo htmlspecialchars($section['row_header']); ?></h3>
                        <?php if (!empty($section['sub_header'])): ?>
                            <p style="margin-left: 2%; opacity: 0.8;"><?php echo htmlspecialchars($section['sub_header']); ?></p>
                        <?php endif; ?>
                        <button class="toggle-btn" onclick="toggleView(this)">Grid</button>
                        <div class="slider img-cont">
                            <button class="arrow left" onclick="slideLeft(this)">&#10094;</button>
                            <button class="arrow right" onclick="slideRight(this)">&#10095;</button>
                            <?php foreach ($section['images'] as $image): ?>
                                <img src="assets/gellary/<?php echo htmlspecialchars($image['image_name']); ?>"
                                    class="slide-img gallery-img"
                                    alt="<?php echo htmlspecialchars($section['row_header']); ?>">
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center py-5">
                    <h3>No gallery images available</h3>
                    <p>Check back later for updates!</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Fullscreen Overlay -->
        <div id="fullscreenOverlay" class="fullscreen-overlay" onclick="closeFullscreen(event)">
            <span class="close-btn" onclick="closeFullscreen(event)">&times;</span>
            <img id="fullscreenImg" src="" alt="">
        </div>
    </main>


    <?php include 'components/footer.php'; ?>
    <script>
        function slideLeft(btn) {
            const slider = btn.closest('.slider');
            slider.scrollBy({
                left: -300,
                behavior: 'smooth'
            });
        }

        function slideRight(btn) {
            const slider = btn.closest('.slider');
            slider.scrollBy({
                left: 300,
                behavior: 'smooth'
            });
        }

        function toggleView(button) {
            const section = button.closest('.slider-section');
            const slider = section.querySelector('.img-cont');
            const leftArrow = section.querySelector('.arrow.left');
            const rightArrow = section.querySelector('.arrow.right');

            if (slider.classList.contains('grid-view')) {
                slider.classList.remove('grid-view');
                slider.classList.add('slider');
                slider.style.display = 'flex';
                leftArrow.style.display = 'block';
                rightArrow.style.display = 'block';
                button.textContent = 'Grid';
            } else {
                slider.classList.remove('slider');
                slider.classList.add('grid-view');
                slider.style.display = 'grid';
                leftArrow.style.display = 'none';
                rightArrow.style.display = 'none';
                button.textContent = 'Slider';
            }
        }

        const overlay = document.getElementById('fullscreenOverlay');
        const fullImg = document.getElementById('fullscreenImg');
        document.addEventListener('click', e => {
            if (e.target.classList.contains('gallery-img')) {
                fullImg.src = e.target.src;
                overlay.classList.add('active');
            }
        });

        function closeFullscreen(e) {
            if (e.target === overlay || e.target.classList.contains('close-btn')) {
                fullImg.style.animation = 'shrinkDown 0.5s forwards';
                setTimeout(() => {
                    overlay.classList.remove('active');
                    fullImg.style.animation = '';
                    fullImg.src = '';
                }, 500);
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>