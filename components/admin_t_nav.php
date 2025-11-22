    <!-- nav.php -->
    <?php
    // Default to 'members' if not set
    $current_tab = $current_tab ?? 'members';
    ?>

    <div class="t_nav d-flex w-100 h-25 p-2 justify-content-center align-items-center gap-3">
        <button
            class="nav-btn <?php echo $current_tab === 'members' ? 'active' : ''; ?>"
            onclick="location.href='index.php'">
            Club Members
        </button>
        <button
            class="nav-btn <?php echo $current_tab === 'executives' ? 'active' : ''; ?>"
            onclick="location.href='executives.php'">
            Executives
        </button>

        <button
            class="nav-btn <?php echo $current_tab === 'requests' ? 'active' : ''; ?>"
            onclick="location.href='requests.php'">
            Requests
        </button>
        <button
            class="nav-btn <?php echo $current_tab === 'gallery' ? 'active' : ''; ?>"
            onclick="location.href='edit_gallery.php'">
            gallery
        </button>
        <button
            class="nav-btn <?php echo $current_tab === 'upcomings_crud' ? 'active' : ''; ?>"
            onclick="location.href='upcomings_crud.php'">
            Upcommings
        </button>
        <button
            class="nav-btn btn-danger"
            onclick="location.href='../funcs/admin_logout.php'"
            style="background-color: #dc3545; color: white; border: none;">
            Logout
        </button>
    </div>