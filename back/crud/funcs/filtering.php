<script>
    const allData = <?php echo json_encode($result); ?>;
    let filteredData = [...allData];

    function renderData() {
        const container = document.getElementById('outputdata');
        container.innerHTML = ''; // Clear previous rows

        let i = 1; // Counter for numbering

        filteredData.forEach(row => {
            const tr = document.createElement('tr');

            tr.innerHTML = `
        <td>${i}</td>
        <td class="d-flex justify-content-center">
            <img src="${row.img || 'default.png'}" class="img-thumbnail" width="60px">
        </td>
        <td>${row.id}</td>
        <td>${row.name}</td>
        <td>${row.batch}</td>
        <td>${row.department}</td>
        <td>${row.mail}</td>
        <td>${row.phone}</td>
        <td>${row.bloodGroup}</td>
    `;

            container.appendChild(tr);
            i++;
        });

    }

    function applyFilters() {
        filteredData = allData.filter(row => {
            return (!data.department || row.department === data.department) &&
                (!data.batch || row.batch === data.batch) &&
                (data.clubs.length === 0 || data.clubs.some(c => row.clubs.includes(c)));
        });
        renderData();
    }

    // call applyFilters() whenever your JS filter changes
</script>