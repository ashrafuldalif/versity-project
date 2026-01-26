<?php
// Security check - must be logged in as admin
require_once '../funcs/check_admin.php';
include '../funcs/connect.php';

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/root.css" rel="stylesheet">
    <link href="../assets/css/scroll-fix.css" rel="stylesheet">
    <link href="../assets/css/admidMembers.css" rel="stylesheet">

</head>

<body>
    <?php
    $current_tab = 'members';
    include '../components/admin_t_nav.php'
    ?>

    <div class="filter-bar d-flex  justify-content-around align-items-center bg-white p-3 rounded shadow-sm">


        <!-- Department Dropdown -->
        <div class="dropdown">
            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                Department
            </button>
            <ul class="dropdown-menu" id="departmentDropdown">
                <!-- CSE
                EEE
                BBA
                TFD
                LHR
                PHR
                ENG -->
                <li data-value="CSE" class="dropdown-item">CSE</li>
                <li data-value="EEE" class="dropdown-item">EEE</li>
                <li data-value="BBA" class="dropdown-item">BBA</li>
                <li data-value="TFD" class="dropdown-item">TFD</li>
                <li data-value="LHR" class="dropdown-item">LHR</li>
                <li data-value="ENG" class="dropdown-item">ENG</li>
                <li data-value="Pharmacy" class="dropdown-item">Pharmacy</li>


            </ul>
        </div>

        <!-- Batch Dropdown -->
        <div class="dropdown">
            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                Batch
            </button>
            <ul class="dropdown-menu" id="batchDropdown">
                <li data-value="17" class="dropdown-item">17</li>
                <li data-value="18" class="dropdown-item">18</li>
                <li data-value="19" class="dropdown-item">19</li>
                <li data-value="20" class="dropdown-item">20</li>
                <li data-value="21" class="dropdown-item">21</li>
                <li data-value="22" class="dropdown-item">22</li>
                <li data-value="23" class="dropdown-item">23</li>
                <li data-value="24" class="dropdown-item">24</li>
                <li data-value="25" class="dropdown-item">25</li>
                <li data-value="26" class="dropdown-item">26</li>
                <li data-value="27" class="dropdown-item">27</li>
                <li data-value="28" class="dropdown-item">28</li>
                <li data-value="29" class="dropdown-item">29</li>
                <li data-value="30" class="dropdown-item">30</li>
                <li data-value="31" class="dropdown-item">31</li>

            </ul>
        </div>

        <!-- Blood Dropdown -->
        <div class="dropdown">
            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                Blood Group
            </button>

            <ul class="dropdown-menu" id="bloodDropdown">
                <li data-value="A+" class="dropdown-item">A+</li>
                <li data-value="A-" class="dropdown-item">A-</li>
                <li data-value="B+" class="dropdown-item">B+</li>
                <li data-value="B-" class="dropdown-item">B-</li>
                <li data-value="AB+" class="dropdown-item">AB+</li>
                <li data-value="AB-" class="dropdown-item">AB-</li>
                <li data-value="O+" class="dropdown-item">O+</li>
                <li data-value="O-" class="dropdown-item">O-</li>

            </ul>
        </div>

        <!-- Club Dropdown with Checkboxes -->
        <div class="dropdown">
            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                Club
            </button>
            <ul class="dropdown-menu px-3" id="clubDropdown">
                <li class="form-check">
                    <input class="form-check-input" type="checkbox" value="Music" id="clubMusic">
                    <label class="form-check-label" for="clubMusic">Music</label>
                </li>
                <li class="form-check">
                    <input class="form-check-input" type="checkbox" value="Sports" id="clubSports">
                    <label class="form-check-label" for="clubSports">Sports</label>
                </li>
                <li class="form-check">
                    <input class="form-check-input" type="checkbox" value="Cultural" id="clubTradition">
                    <label class="form-check-label" for="clubTradition">Cultural</label>
                </li>
                <li class="form-check">
                    <input class="form-check-input" type="checkbox" value="Art" id="clubArt">
                    <label class="form-check-label" for="clubArt">Art</label>
                </li>
                <li class="form-check">
                    <input class="form-check-input" type="checkbox" value="Drama" id="clubCodeforce">
                    <label class="form-check-label" for="clubCodeforce">Drama</label>
                </li>
                <li class="form-check">
                    <input class="form-check-input" type="checkbox" value="Photography" id="clubHacking">
                    <label class="form-check-label" for="clubHacking">Photography</label>
                </li>

                <li class="form-check">
                    <input class="form-check-input" type="checkbox" value="Programming" id="clubHacking">
                    <label class="form-check-label" for="clubHacking">Programming</label>
                </li>
                <li class="form-check">
                    <input class="form-check-input" type="checkbox" value="Robotics" id="clubHacking">
                    <label class="form-check-label" for="clubHacking">Robotics</label>
                </li>
                <li class="form-check">
                    <input class="form-check-input" type="checkbox" value="Debate" id="clubHacking">
                    <label class="form-check-label" for="clubHacking">Debate</label>
                </li>
                <li class="form-check">
                    <input class="form-check-input" type="checkbox" value="Volunteer" id="clubHacking">
                    <label class="form-check-label" for="clubHacking">Volunteer</label>
                </li>
            </ul>
        </div>

        `<!-- Search By Dropdown -->
        <div class="dropdown">
            <button class="btn btn-info text-white dropdown-toggle bg-primary"
                type="button" data-bs-toggle="dropdown">
                Search By
            </button>
            <ul class="dropdown-menu" id="searchBy">
                <li class="dropdown-item" data-field="name">Name</li>
                <li class="dropdown-item" data-field="id">ID</li>
                <li class="dropdown-item" data-field="mail">Email</li>
            </ul>
        </div>

        <!-- Selected Display -->
        <div id="selectedSearchBy" class="fw-semibold text-secondary">Name</div>

        <!-- Search Input (now with autocomplete) -->
        <div class="position-relative flex-grow-1">
            <input type="search" id="searchInput" class="form-control "
                placeholder="SEEKING WHO?" autocomplete="off">
            <!-- suggestions dropdown -->
            <ul id="suggestions" class="dropdown-menu w-100" style="display:none;"></ul>
        </div>

        <button id="searchBtn" class="btn btn-success">Search</button>`

    </div>
    <div class="bg-white p-3 rounded shadow-sm d-flex flex-wrap gap-2">
        <div id="dptTocken" class="filter-tag d-flex align-items-center gap-2 px-3 py-1 rounded-pill border bg-light flex-wrap">
            <strong class="me-2">Department:</strong>

            <!-- <span class="d-inline-flex align-items-center bg-white border rounded-pill px-2 py-1">
                <span class="fs-6 fw-normal me-1" id="dptTxt">CSE</span>
                <button class="btn-close btn-sm m-0 cls-easy" style="font-size: 0.6rem;"></button>
            </span> -->


        </div>
        <div id="btchTocken" class="filter-tag d-flex align-items-center gap-2 px-3 py-1 rounded-pill border bg-light flex-wrap">
            <strong class="me-2">Batch:</strong>

            <!-- <span class="d-inline-flex align-items-center bg-white border rounded-pill px-2 py-1">
                <span class="fs-6 fw-normal me-1" id="batchTxt">31</span>
                <button class="btn-close btn-sm m-0 cls-easy" style="font-size: 0.6rem;"></button>
            </span> -->


        </div>

        <div id="bloodTocken" class="filter-tag d-flex align-items-center gap-2 px-3 py-1 rounded-pill border bg-light flex-wrap">
            <strong class="me-2">Blood Group:</strong>

            <!-- <span class="d-inline-flex align-items-center bg-white border rounded-pill px-2 py-1">
                <span class="fs-6 fw-normal me-1" id="batchTxt">31</span>
                <button class="btn-close btn-sm m-0 cls-easy" style="font-size: 0.6rem;"></button>
            </span> -->


        </div>
        <div id="clubTockens" class="filter-tag d-flex align-items-center gap-2 px-3 py-1 rounded-pill border bg-light flex-wrap">
            <strong class="me-2">Club :</strong>

            <!-- <span class="d-inline-flex align-items-center bg-white border rounded-pill px-2 py-1">
                <span class="fs-6 fw-normal me-1">sports</span>
                <button class="btn-close btn-sm m-0 cls-easy" style="font-size: 0.6rem;"></button>
            </span>

            <span class="d-inline-flex align-items-center bg-white border rounded-pill px-2 py-1">
                <span class="fs-6 fw-normal me-1">music</span>
                <button class="btn-close btn-sm m-0 cls-easy" style="font-size: 0.6rem;"></button>
            </span> -->


        </div>


        <button class="d-inline-flex align-items-center  border rounded-pill px-2 py-1" onclick="clearall()">
            <span class="fs-6 fw-normal me-1">Clear All</span>
        </button>
    </div>


    <div class="table-responsive">
        <table class="table table-bordered mt-4">
            <thead class="table-dark">
                <tr>
                    <th>no</th>
                    <th>image</th>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Batch</th>
                    <th>department</th>
                    <th>Clubs</th>
                    <th>Email</th>
                    <th>phone</th>
                    <th>Blood</th>
                </tr>
            </thead>
            <tbody id="outputdata">


            <?php
            $sql = "SELECT `id`, `name`, `batch`, `mail`, `img`, `bloodGroup`, `department`, `phone` FROM `club_members` ORDER BY `name` ASC";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->get_result();
            $i = 1;
            $storage = [];
            while ($row = $result->fetch_assoc()) {

                $name = $row['name'];
                $id = $row['id'];
                $batch = $row['batch'];
                $mail = $row['mail'];
                $imgl = $row['img'];
                $imgl = "./assets/members/" . $imgl;
                $row['img'] = $imgl;
                $bgroup = $row['bloodGroup'];
                $phone = $row['phone'];
                $department = $row['department'];



                // Fetch clubs for this member
                $clubSql = "
                                SELECT c.name 
                                FROM member_clubs mc
                                JOIN clubs c ON mc.club_id = c.id
                                WHERE mc.member_id = ?
                            ";
                $clubStmt = $conn->prepare($clubSql);
                $clubStmt->bind_param('i', $id);
                $clubStmt->execute();
                $clubResult = $clubStmt->get_result();

                $clubs = [];
                while ($clubRow = $clubResult->fetch_assoc()) {
                    $clubs[] = $clubRow['name'];
                }

                $row['clubs'] = $clubs; // attach clubs array to the member
                $storage[] = $row;
                $joinClbs = '';
                foreach ($clubs as $club) {

                    $joinClbs .= $club . ' , ';
                }

                echo "
                              <tr>            
                              <td data-label='No'>$i</td>
                        <td data-label='Image' class=\"d-flex justify-content-center\"><img src=\"${imgl}\" class=\"img-thumbnail cprofile\"></td>
                        <td data-label='ID'>$id</td>
                        <td data-label='Name'>$name</td>
                        <td data-label='Batch'>$batch</td>
                        <td data-label='Department'>$department</td>
                        <td data-label='Clubs'>$joinClbs</td>
                        <td data-label='Email'>$mail</td>
                        <td data-label='Phone'>$phone</td>
                        <td data-label='Blood'>$bgroup</td>
                    </tr>
                        ";
                $i++;
            }



            $stmt->close();
            $conn->close();

            ?>

            </tbody>
        </table>
    </div>

    <script src="http://localhost:35729/livereload.js"></script>
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>


    <!-- filter js  -->
    <script>
        let data = {
            department: null,
            batch: null,
            bloodG: null,
            clubs: []
        };

        const dptTxt = document.getElementById('dptTxt');
        const batchTxt = document.getElementById('batchTxt');
        const bloodTxt = document.getElementById('bloodTxt');
        const clubTockens = document.getElementById('clubTockens');
        const btchTocken = document.getElementById('btchTocken');
        const bloodTocken = document.getElementById('bloodTocken');
        const dptTocken = document.getElementById('dptTocken');
        const clsEasy = document.querySelectorAll('.cls-easy');
        // const clubCheckbox = document.querySelector(`#clubDropdown input[value="${value}"]`);

        // // Department selection
        // document.querySelectorAll('#departmentDropdown li').forEach(item => {
        //     item.addEventListener('click', () => {
        //         data.department = item.dataset.value;

        //         dptTocken.querySelectorAll('span').forEach(e => {
        //             e.remove();
        //         });

        //         createTag(dptTocken, data.department, "dptTxt");

        //         console.log('Selected Department:', data.department);

        //         applyFilters();
        //     });
        // });

        // // Batch selection
        // document.querySelectorAll('#batchDropdown li').forEach(item => {
        //     item.addEventListener('click', () => {
        //         data.batch = item.dataset.value;

        //         btchTocken.querySelectorAll('span').forEach(e => {
        //             e.remove();
        //         });

        //         createTag(btchTocken, data.batch, "batchTxt");

        //         console.log('Selected Batch:', data.batch);
        //         applyFilters();
        //     });
        // });


        // // blood group selection
        // document.querySelectorAll('#bloodDropdown li').forEach(item => {
        //     item.addEventListener('click', () => {
        //         data.bloodG = item.dataset.value;

        //         bloodTocken.querySelectorAll('span').forEach(e => {
        //             e.remove();
        //         });

        //         createTag(bloodTocken, data.bloodG, "bloodTxt");

        //         console.log('Selected blood:', data.bloodG);
        //         applyFilters();
        //     });
        // });

        // Unified dropdown handler for Department, Batch, and Blood Group
        function setupDropdown(dropdownId, dataKey, tokenId, tagId) {
            document.querySelectorAll(`#${dropdownId} li`).forEach(item => {
                item.addEventListener('click', () => {
                    const value = item.dataset.value;

                    // Update data
                    data[dataKey] = value;

                    // Clear previous tag
                    const token = document.getElementById(tokenId);
                    token.querySelectorAll('span').forEach(e => e.remove());

                    // Create new tag
                    createTag(token, value, tagId);

                    console.log(`Selected ${dataKey}:`, value);
                    applyFilters();
                });
            });
        }

        // Call it for each dropdown
        setupDropdown('departmentDropdown', 'department', 'dptTocken', 'dptTxt');
        setupDropdown('batchDropdown', 'batch', 'btchTocken', 'batchTxt');
        setupDropdown('bloodDropdown', 'bloodG', 'bloodTocken', 'bloodTxt');
        // Club checkboxes
        document.querySelectorAll('#clubDropdown input[type="checkbox"]').forEach(chk => {
            chk.addEventListener('change', () => {
                data.clubs = Array.from(document.querySelectorAll('#clubDropdown input[type="checkbox"]:checked'))
                    .map(cb => cb.value);
                clubTockens.querySelectorAll('.d-inline-flex').forEach(el => el.remove());
                data.clubs.forEach(e => {
                    createTag(clubTockens, e);
                })
                console.log('Selected Clubs:', data.clubs);
                applyFilters();
            });
        });

        // function sayHello() {
        //     console.log(data);
        // }

        // setInterval(sayHello, 3000);

        // Close buttons
        // document.addEventListener('DOMContentLoaded', () => {
        //     clsEasy.forEach(cls => {
        //         cls.addEventListener('click', closOparation(cls));
        //     });
        // });

        // function closOparation(cls) {

        //     const tagSpan = cls.closest('.d-inline-flex');
        //     const value = tagSpan.querySelector('span')?.innerText;
        //     console.log(value);

        //     // Remove the closest span
        //     tagSpan.remove();

        //     // Check DOM after removal
        //     if (!document.querySelector('#dptTxt')) {
        //         console.log('hello')
        //         data.department = null;

        //     } else if (!document.querySelector('#batchTxt')) {
        //         data.batch = null;


        //     } else {
        //         // For clubs — uncheck matching checkbox
        //         const clubCheckbox = document.querySelector(`#clubDropdown input[value="${value}"]`);
        //         if (clubCheckbox) clubCheckbox.checked = false;
        //         data.clubs = data.clubs.filter(item => item !== value);
        //     }


        //     console.log('Updated data after deletion:', data);

        // }

        function createTag(parent, spanTxt, aidi = null) {
            // Create the outer span
            const wrapper = document.createElement('span');
            wrapper.className = 'd-inline-flex align-items-center bg-white border rounded-pill px-2 py-1';

            // Create the text span
            const textSpan = document.createElement('span');
            textSpan.className = 'fs-6 fw-normal me-1';
            if (aidi) textSpan.id = aidi;
            textSpan.textContent = spanTxt;

            // Create the close button
            const closeBtn = document.createElement('button');
            closeBtn.className = 'btn-close btn-sm m-0 cls-easy';
            closeBtn.style.fontSize = '0.6rem';

            // Append children
            wrapper.appendChild(textSpan);
            wrapper.appendChild(closeBtn);

            // Add close functionality
            closeBtn.addEventListener('click', () => {
                const wrapper = closeBtn.closest('.d-inline-flex');
                const textSpan = wrapper.querySelector('span');
                const value = textSpan?.innerText;

                // Remove from clubs if it exists
                const clubIndex = data.clubs.indexOf(value);
                if (clubIndex > -1) {
                    data.clubs.splice(clubIndex, 1);
                    // Uncheck corresponding checkbox
                    const clubCheckbox = document.querySelector(`#clubDropdown input[value="${value}"]`);
                    if (clubCheckbox) clubCheckbox.checked = false;
                }

                // Check if this was the department tag
                if (textSpan?.id === 'dptTxt') {
                    data.department = null;
                }

                // Check if this was the batch tag
                if (textSpan?.id === 'batchTxt') {
                    data.batch = null;
                }

                if (textSpan?.id === 'bloodTxt') {
                    data.bloodG = null;
                }

                // Remove the tag from DOM
                wrapper.remove();
                applyFilters();
                console.log('Updated data after deletion:', JSON.parse(JSON.stringify(data)));
            });


            // Append to parent
            parent.appendChild(wrapper);
        }

        function clearall() {
            const clsEasy = document.querySelectorAll('.cls-easy');
            clsEasy.forEach(cls => {
                cls.closest('span').remove();
                // cls.closest('span').classList.add("d-none")
                // console.log(cls.closest('span').classList);
            })
            document.querySelectorAll('input[type="checkbox"').forEach(c => {
                c.checked = false;
            })

            data.batch = null;
            data.department = null;
            data.bloodG = null;
            data.clubs = [];
            applyFilters();
        }

        const allData = <?php echo json_encode($storage, JSON_PRETTY_PRINT); ?>;
        let filteredData = [...allData];
        // filteredData.sort((a, b) => a.name.localeCompare(b.name));


        // filteredData.forEach(e => {
        //     console.log(e.name)
        // })

        function renderData() {
            const container = document.getElementById('outputdata');
            container.innerHTML = ''; // Clear previous rows

            let i = 1; // Counter for numbering

            filteredData.forEach(e => {
                console.log(e.img);
                const tr = document.createElement('tr');
                // tr.style.backgroundColor = "gray";
                // Example: Highlight every even row


                // join club names properly
                let joinClbs = '';
                if (Array.isArray(e.clubs)) {
                    joinClbs = e.clubs.join(', ');
                }
                tr.innerHTML = `
            <td data-label='No'>${i}</td>
            
            <td data-label='Image' class="d-flex justify-content-center">
               
                <img src="${e.img || 'default.png'}" class="img-thumbnail cprofile">
                </td>
            <td data-label='ID'>${e.id}</td>
            <td data-label='Name'>${e.name}</td>
            <td data-label='Batch'>${e.batch}</td>
            <td data-label='Department'>${e.department}</td>
            <td data-label='Clubs'>${joinClbs}</td>
            <td data-label='Email'>${e.mail}</td>
            <td data-label='Phone'>${e.phone}</td>
            <td data-label='Blood'>${e.bloodGroup}</td>
        `;

                container.appendChild(tr);
                i++;
            });
        }


        function applyFilters() {
            filteredData = allData.filter(row => {
                row.batch = parseInt(row.batch);
                data.batch = parseInt(data.batch);
                
                // Check if all selected clubs are present (AND logic instead of OR)
                const clubsMatch = data.clubs.length === 0 || 
                    data.clubs.every(selectedClub => row.clubs.includes(selectedClub));
                
                return (!data.department || row.department === data.department) &&
                    (!data.batch || row.batch === data.batch) &&
                    (!data.bloodG || row.bloodGroup === data.bloodG) &&
                    clubsMatch;
            });
            renderData();
        }

        // call applyFilters() whenever your JS filter changes
    </script>
    <!-- search js  -->
    <script>
        let searchField = 'name'; // default column
        const searchInput = document.getElementById('searchInput');
        const suggestionsUl = document.getElementById('suggestions');
        const selectedDisp = document.getElementById('selectedSearchBy');

        /* ---- 1. Change “Search By” column ---- */
        document.querySelectorAll('#searchBy li').forEach(li => {
            li.addEventListener('click', () => {
                searchField = li.dataset.field;
                selectedDisp.textContent = li.textContent;
                searchInput.focus();
            });
        });

        /* ---- 2. Real‑time suggestions while typing ---- */
        searchInput.addEventListener('input', () => {
            const term = searchInput.value.trim().toLowerCase();
            suggestionsUl.innerHTML = '';
            suggestionsUl.style.display = 'none';

            if (!term) return; // empty → hide

            const matches = allData.filter(row => {
                const value = String(row[searchField] ?? '').toLowerCase();
                return value.includes(term);
            });

            if (matches.length === 0) return;

            // Sort matches alphabetically by the search field
            matches.sort((a, b) => {
                const aValue = String(a[searchField] ?? '').toLowerCase();
                const bValue = String(b[searchField] ?? '').toLowerCase();
                return aValue.localeCompare(bValue);
            });

            // limit to 8 suggestions (feel free to change)
            matches.slice(0, 8).forEach(row => {
                const li = document.createElement('li');
                li.className = 'dropdown-item suggestion-item';
                li.textContent = row[searchField];
                li.dataset.id = row.id; // keep the member id
                li.addEventListener('click', () => selectSuggestion(row));
                suggestionsUl.appendChild(li);
            });

            suggestionsUl.style.display = 'block';
        });

        /* ---- 3. Click a suggestion → fill input + filter ---- */
        function selectSuggestion(row) {
            searchInput.value = row[searchField];
            suggestionsUl.style.display = 'none';

            // filter to show ONLY this member
            filteredData = allData.filter(r => r.id === row.id);
            renderData();
        }

        /* ---- 4. Hide suggestions when clicking outside ---- */
        document.addEventListener('click', e => {
            if (!searchInput.contains(e.target) && !suggestionsUl.contains(e.target)) {
                suggestionsUl.style.display = 'none';
            }
        });

        /* ---- 5. Optional: “Search” button = same as picking first suggestion ---- */
        document.getElementById('searchBtn').addEventListener('click', () => {
            const term = searchInput.value.trim().toLowerCase();
            if (!term) return applyFilters(); // no term → reset

            const firstMatch = allData.find(row => {
                const val = String(row[searchField] ?? '').toLowerCase();
                return val.includes(term);
            });

            if (firstMatch) {
                filteredData = allData.filter(r => r.id === firstMatch.id);
                renderData();
            } else {
                alert('No match found');
            }
        });
    </script>
</body>

</html>