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
    <style>
        body {
            background: var(--background-cream);
            color: var(--text-dark);
            font-family: 'Poppins', sans-serif;
            overflow-x: hidden;
            margin-top: 76px;
            position: relative;
        }

        h3 {
            margin-left: 2%;
            margin-top: 1rem;
            font-weight: 600;
            color: var(--primary-color);
        }

        .slider-section {
            position: relative;
            margin-bottom: 2rem;
            transition: all 0.3s ease;
        }

        .slider {
            display: flex;
            align-items: end;
            overflow-x: auto;
            scroll-behavior: smooth;
            scrollbar-width: none;
            padding: 1rem 2%;
        }

        .slider::-webkit-scrollbar {
            display: none;
        }

        .slide-img {
            min-width: 250px;
            height: 150px;
            margin-right: 15px;
            border-radius: 8px;
            object-fit: cover;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .slide-img:hover {
            transform: scale(1.1);
            z-index: 2;
        }

        .arrow {
            position: absolute;
            height: 150px;
            background: var(--overlay-dark);
            border: none;
            color: var(--text-on-dark);
            font-size: 2rem;
            padding: 0 10px;
            cursor: pointer;
            z-index: 5;
            transition: opacity 0.2s;
        }

        .arrow:hover {
            opacity: 0.8;
        }

        .arrow.left {
            left: 0;
        }

        .arrow.right {
            right: 0;
        }

        .toggle-btn {
            position: absolute;
            top: 20px;
            right: 15px;
            z-index: 10;
            background: var(--accent-color);
            color: var(--text-dark);
            border: none;
            padding: 6px 10px;
            border-radius: 5px;
            cursor: pointer;
            margin-bottom: 10rem;
            font-size: 14px;
            font-weight: 600;
        }

        .toggle-btn:hover {
            background: var(--secondary-hover);
        }

        .grid-view {
            display: grid !important;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 10px;
            overflow: hidden;
            padding: 1rem 2%;
        }

        .grid-view img {
            width: 100%;
            height: clamp(100px, 200px, 220px);
            object-fit: contain;
            border-radius: 8px;
            cursor: pointer;
            transition: transform 0.3s;
        }

        .grid-view img:hover {
            transform: scale(1.05);
        }

        .fullscreen-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: var(--overlay-dark);
            display: flex;
            justify-content: center;
            align-items: center;
            visibility: hidden;
            opacity: 0;
            transition: opacity 0.4s ease, visibility 0.4s;
            z-index: 9999;
        }

        .fullscreen-overlay.active {
            visibility: visible;
            opacity: 1;
        }

        .fullscreen-overlay img {
            max-width: 80vw;
            max-height: 80vh;
            border-radius: 10px;
            transition: transform 0.4s ease;
        }

        .close-btn {
            position: absolute;
            top: 20px;
            right: 30px;
            font-size: 35px;
            color: var(--text-on-dark);
            cursor: pointer;
            z-index: 10000;
        }

        @media (max-width: 600px) {
            .slide-img {
                min-width: 140px;
                height: 90px;
                margin-right: 10px;
            }

            .slider {
                padding: 0.5rem 4%;
            }

            .arrow {
                height: 90px;
                font-size: 1.4rem;
                padding: 0 8px;
            }

            .grid-view {
                grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
                gap: 8px;
                padding: 0.5rem 4%;
            }

            .grid-view img {
                height: clamp(80px, 140px, 160px);
                object-fit: cover;
            }

            h3 {
                font-size: 1rem;
                margin-left: 3%;
            }
        }
    </style>
</head>

<body>
    <?php include 'components/navbar.php'; ?>

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