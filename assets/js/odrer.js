document.addEventListener('DOMContentLoaded', function() {
    // Enable quantity and instructions when item is checked
    document.querySelectorAll('input[type="checkbox"][name="items[]"]').forEach(checkbox => {
        const itemId = checkbox.value;
        const quantityInput = document.querySelector(`input[name="quantity[${itemId}]"]`);
        const instructionsInput = document.querySelector(`input[name="instructions[${itemId}]"]`);
        
        function toggleInputs() {
            quantityInput.disabled = !checkbox.checked;
            instructionsInput.disabled = !checkbox.checked;
            if (!checkbox.checked) {
                quantityInput.value = 1;
                instructionsInput.value = '';
            }
        }
        
        checkbox.addEventListener('change', toggleInputs);
        toggleInputs(); // Initialize state
    });
    
    // Time slot selection for dine-in
    document.querySelectorAll('.time-slot').forEach(button => {
        button.addEventListener('click', function() {
            document.querySelectorAll('.time-slot').forEach(btn => {
                btn.classList.remove('active');
            });
            this.classList.add('active');
            document.getElementById('reservation_time').value = this.dataset.time;
        });
    });
    
    // Delivery time selection
    const deliveryTimeSelect = document.getElementById('delivery_time');
    const specificTimeInput = document.getElementById('specific_delivery_time');
    
    if (deliveryTimeSelect) {
        deliveryTimeSelect.addEventListener('change', function() {
            if (this.value === 'specific') {
                specificTimeInput.classList.remove('hidden');
                specificTimeInput.required = true;
            } else {
                specificTimeInput.classList.add('hidden');
                specificTimeInput.required = false;
            }
        });
    }
    
    // Table visualization for dine-in
    if (document.getElementById('tablesContainer')) {
        initTableVisualization();
    }
    
    // Initialize category filtering
    initCategoryFiltering();
});

function initTableVisualization() {
    const container = document.getElementById('tablesContainer');
    const dateInput = document.getElementById('reservation_date');
    const timeSelect = document.getElementById('reservation_time');
    const tableSelect = document.getElementById('table_number');
    
    // Sample table data (in a real app, this would come from the database)
    const tables = [
        <?php foreach ($available_tables as $table): ?>
        {
            id: <?= $table['table_id'] ?>,
            number: '<?= $table['table_number'] ?>',
            capacity: <?= $table['capacity'] ?>,
            x: <?= rand(10, 80) ?>,
            y: <?= rand(10, 80) ?>
        },
        <?php endforeach; ?>
    ];
    
    // Render tables on floor plan
    function renderTables() {
        container.innerHTML = '';
        
        tables.forEach(table => {
            const tableElement = document.createElement('div');
            tableElement.className = `table-item absolute bg-green-500 text-white rounded-lg flex items-center justify-center font-bold cursor-pointer`;
            tableElement.style.left = `${table.x}%`;
            tableElement.style.top = `${table.y}%`;
            tableElement.style.width = table.capacity <= 2 ? '50px' : 
                                     table.capacity <= 4 ? '65px' : 
                                     table.capacity <= 6 ? '80px' : '95px';
            tableElement.style.height = table.capacity <= 2 ? '50px' : 
                                      table.capacity <= 4 ? '65px' : 
                                      table.capacity <= 6 ? '80px' : '95px';
            tableElement.dataset.tableId = table.id;
            
            tableElement.innerHTML = `
                <div class="text-center">
                    <div class="text-lg">${table.number}</div>
                    <div class="text-xs">${table.capacity} seats</div>
                </div>
            `;
            
            tableElement.addEventListener('click', () => selectTable(table));
            container.appendChild(tableElement);
        });
    }
    
    // Select table handler
    function selectTable(table) {
        if (!dateInput.value || !timeSelect.value) {
            alert('Please select a date and time first!');
            return;
        }
        
        // Clear previous selection
        document.querySelectorAll('.table-item').forEach(el => {
            el.classList.remove('table-selected', 'bg-blue-500');
            if (!el.classList.contains('table-booked')) {
                el.classList.add('bg-green-500');
            }
        });
        
        // Select new table
        const tableElement = document.querySelector(`[data-table-id="${table.id}"]`);
        tableElement.classList.remove('bg-green-500');
        tableElement.classList.add('bg-blue-500', 'table-selected');
        
        // Update the hidden select
        tableSelect.value = table.id;
    }
    
    // Initialize
    renderTables();
    
    // Simulate table availability based on date/time
    dateInput.addEventListener('change', simulateTableAvailability);
    timeSelect.addEventListener('change', simulateTableAvailability);
    
    function simulateTableAvailability() {
        if (!dateInput.value || !timeSelect.value) return;
        
        // Simulate some booked tables (in real app, fetch from server)
        const bookedTables = [
            tables[0].id, 
            tables[3].id
        ];
        
        document.querySelectorAll('.table-item').forEach(tableElement => {
            const tableId = parseInt(tableElement.dataset.tableId);
            const isBooked = bookedTables.includes(tableId);
            
            tableElement.classList.remove('bg-green-500', 'bg-red-500', 'bg-blue-500', 'table-selected');
            
            if (isBooked) {
                tableElement.classList.add('bg-red-500', 'table-booked');
                tableElement.style.cursor = 'not-allowed';
            } else {
                tableElement.classList.add('bg-green-500');
                tableElement.style.cursor = 'pointer';
            }
        });
    }
}

function initCategoryFiltering() {
    // Set initial active state for "All Items" button
    document.querySelector('.category-filter[data-category="all"]').classList.add('bg-amber-600', 'text-white');
    document.querySelector('.category-filter[data-category="all"]').classList.remove('bg-amber-100', 'text-amber-800');
    
    // Category filtering
    document.querySelectorAll('.category-filter').forEach(button => {
        button.addEventListener('click', function() {
            // Update active state of filter buttons
            document.querySelectorAll('.category-filter').forEach(btn => {
                btn.classList.remove('bg-amber-600', 'text-white');
                btn.classList.add('bg-amber-100', 'text-amber-800');
            });
            this.classList.remove('bg-amber-100', 'text-amber-800');
            this.classList.add('bg-amber-600', 'text-white');
            
            const category = this.dataset.category;
            
            // Show/hide items based on category
            document.querySelectorAll('.category-item, .category-section').forEach(element => {
                if (category === 'all' || element.dataset.category === category) {
                    element.classList.remove('hidden');
                } else {
                    element.classList.add('hidden');
                }
            });
            
            // Show category headers only if they have visible items
            document.querySelectorAll('.category-section').forEach(section => {
                const categoryId = section.dataset.category;
                if (category !== 'all' && category !== categoryId) {
                    section.classList.add('hidden');
                    return;
                }
                
                const hasVisibleItems = !!document.querySelector(`.category-item[data-category="${categoryId}"]:not(.hidden)`);
                section.classList.toggle('hidden', !hasVisibleItems);
            });
        });
    });
}