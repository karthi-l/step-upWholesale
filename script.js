
    document.addEventListener('DOMContentLoaded', function () {
// Elements
const stockAvailableElement = document.getElementById('stock_available');
const sizeVariationSection = document.getElementById('size-variation-section');
const setsAvailableSection = document.getElementById('sets-available-section');
const customSizesSection = document.getElementById('custom-sizes-section');
const customSizesContainer = document.getElementById('custom-sizes-container');
const sizeVariationElement = document.getElementById('size_variation');

// Initial State: Only show stock available section
sizeVariationSection.style.display = "none";
setsAvailableSection.style.display = "none";
customSizesSection.style.display = "none";

// Stock available change handler
stockAvailableElement.addEventListener('change', function () {
const stockAvailable = this.value;

// Reset all sections
sizeVariationSection.style.display = "none";
setsAvailableSection.style.display = "none";
customSizesSection.style.display = "none";

if (stockAvailable === "Yes") {
    // Show size variation section
    sizeVariationSection.style.display = "block";

    // Trigger size variation change to apply logic
    sizeVariationElement.dispatchEvent(new Event('change'));
}
});

// Size variation change handler
sizeVariationElement.addEventListener('change', function () {
const sizeVariation = this.value;

// Reset sections dependent on size variation
setsAvailableSection.style.display = "none";
customSizesSection.style.display = "none";

if (sizeVariation === "Custom-Sizes") {
    // Show custom sizes section
    customSizesSection.style.display = "block";
    // Clear any previous custom sizes
    customSizesContainer.innerHTML = "";
} else if (sizeVariation) {
    
    setsAvailableSection.style.display = "block";
} else{
    //Show sets available section
    setsAvailableSection.style.display = "none";
}

});
});


    function addCustomSize() {
        const container = document.getElementById('custom-sizes-container');
        const sizeInput = document.createElement('div');
        sizeInput.className = "m-auto d-flex justify-content-center mb-2 col-xs-12 col-md-6 col-lg-4 col-xl-3";
        sizeInput.innerHTML = `
            <input type="text" name="custom_sizes[]" class="form-control me-2" placeholder="Size" required>
            <input type="number" name="custom_stocks[]" class="form-control me-2" placeholder="Stock" required>
            <button type="button" class="btn btn-danger" onclick="removeCustomSize(this)">Remove</button>
        `;
        container.appendChild(sizeInput);
    }

    function removeCustomSize(button) {
        button.parentElement.remove();
    }
    const sizeOptions = {
    "Gents": ["6*10", "7*10", "8*10", "9*10", "6*9", "7*9", "8*9", "6*7", "6*8", "7*8", "Custom-Sizes"],
    "Gents-Big": ["11*14", "12*14", "13*14", "11*13", "12*13", "11*12", "Custom-Sizes"],
    "Ladies": ["5*9", "6*9", "7*9", "8*9", "5*8", "6*8", "7*8", "6*7", "5*7", "5*6", "Custom-Sizes"],
    "Ladies-Big": ["10*11", "Custom-Sizes"],
    "Boys": {
        "Small": ["8*11", "9*11", "10*11", "8*10", "9*10", "8*9", "Custom-Sizes"],
        "Medium": ["10*11", "11*1", "12*1", "13*1", "10*13", "11*13", "12*13", "10*12", "11*12", "10*11", "Custom-Sizes"],
        "Big": ["1*5", "2*5", "3*5", "4*5", "1*4", "2*4", "3*4", "1*3", "2*3", "1*2", "Custom-Sizes"]
    },
    "Girls": {
        "Small": ["8*10", "9*10", "8*9", "Custom-Sizes"],
        "Medium": ["11*13", "12*13", "11*12", "Custom-Sizes"],
        "Big": ["1*4", "2*4", "3*4", "1*3", "2*3", "1*2", "Custom-Sizes"]
    },
    "Kids": {
        "Small": ["15*19", "16*19", "17*19", "18*19", "15*18", "16*18", "17*18", "15*17", "16*17", "15*16", "Custom-Sizes"],
        "Medium": ["5*10", "6*10", "7*10", "8*10", "9*10", "5*9", "6*9", "7*9", "8*9", "Custom-Sizes"]
    },
    "School-Shoes-Boys": [
        "8*11", "9*11", "10*11", "8*10", "9*10", "8*9", "12*1", "13*1", "12*13", "2*5", "3*5", "4*5", "6*10", "7*10", "8*10",
        "9*10", "6*9", "7*9", "8*9", "6*7", "6*8", "7*8", "Custom-Sizes"
    ],
    "School-Shoes-Girls": [
        "8*11", "9*11", "10*11", "8*10", "9*10", "8*9", "12*1", "13*1", "12*13", "2*7", "3*7", "4*7",
        "5*7", "6*7", "2*6", "3*6", "4*6", "5*6", "2*5", "3*5", "4*5", "2*4", "3*4", "2*3", "Custom-Sizes"
    ]
};

    function updateSizeOptions() {
        const commodity = document.getElementById('commodity').value;
        const sizeSelect = document.getElementById('size_variation');
        sizeSelect.innerHTML = "";

        if (sizeOptions[commodity]) {
            const sizes = sizeOptions[commodity];
            if (Array.isArray(sizes)) {
                sizes.forEach(size => {
                    const option = document.createElement('option');
                    option.value = size;
                    option.textContent = size;
                    sizeSelect.appendChild(option);
                });
            } else {
                Object.keys(sizes).forEach(category => {
                    const group = document.createElement('optgroup');
                    group.label = category;
                    sizes[category].forEach(size => {
                        const option = document.createElement('option');
                        option.value = size;
                        option.textContent = size;
                        group.appendChild(option);
                    });
                    sizeSelect.appendChild(group);
                });
            }
        } else {
            const defaultOption = document.createElement('option');
            defaultOption.value = "";
            defaultOption.textContent = "No sizes available";
            sizeSelect.appendChild(defaultOption);
        }
    }
