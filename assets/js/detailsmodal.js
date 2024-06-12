function openDetailsModal(listingId, modalType = 'index') {
    fetch(`/woningmakelaar/assets/php/fetch_listing_details.php?id=${listingId}`)
        .then(response => response.json())
        .then(listing => {
            const modal = modalType === 'woningen' ? document.getElementById('woningenDetailsModal') : document.getElementById('detailsModal');

            if (!modal) {
                console.error('Modal not found:', modalType);
                return;
            }

            const modalContent = modal.querySelector('.details-content');
            if (!modalContent) {
                console.error('Modal content not found in:', modalType);
                return;
            }

            let extraImagesArray = listing.extra_images && typeof listing.extra_images === 'string' ? listing.extra_images.split(',') : [];
            const liggingList = listing.ligging ? `<ul>${listing.ligging.map(item => `<li>${item}</li>`).join('')}</ul>` : '';
            const eigenschappenList = listing.eigenschappen ? `<ul>${listing.eigenschappen.map(item => `<li>${item}</li>`).join('')}</ul>` : '';

            // Admin-specific modal content for admin.php
            if (window.location.pathname.includes('admin.php')) {
                modalContent.innerHTML = `
                    <div class="img-and-text">
                        <div class="imgs-holder">
                            <img src="/woningmakelaar/assets/php/${listing.cover_image_path}" class="main-img" alt="Main Image">
                            <div class="detail-listing-images">
                                ${extraImagesArray.map(image => `<img src="/woningmakelaar/assets/php/${image.trim()}" alt="Image">`).join('')}
                            </div>
                        </div>    
                        <div class="detail-text">            
                            <h2>${listing.address}</h2>
                            <p>${listing.beschrijving}</p>
                        </div>
                    </div>      
                    <div class="filter-and-button"> 
                        <div class="filter-lists">
                            <div class="details-ligging"><h3>Ligging</h3>${liggingList}</div>
                            <div class="details-eigenschappen"><h3>Eigenschappen</h3>${eigenschappenList}</div>
                        </div>
                        <div class="admin-actions">
                            <button class="remove-listing" onclick="removeListing(${listingId})">Remove</button>
                        </div>
                    </div>
                `;
            } else {
                // Default modal content for other pages
                modalContent.innerHTML = `
                    <div class="img-and-text">
                        <div class="imgs-holder">
                            <img src="/woningmakelaar/assets/php/${listing.cover_image_path}" class="main-img" alt="Main Image">
                            <div class="detail-listing-images">
                                ${extraImagesArray.map(image => `<img src="/woningmakelaar/assets/php/${image.trim()}" alt="Image">`).join('')}
                            </div>
                        </div>    
                        <div class="detail-text">            
                            <h2>${listing.address}</h2>
                            <p>${listing.beschrijving}</p>
                        </div>
                    </div>      
                    <div class="filter-and-button"> 
                        <div class="filter-lists">
                            <div class="details-ligging"><h3>Ligging</h3>${liggingList}</div>
                            <div class="details-eigenschappen"><h3>Eigenschappen</h3>${eigenschappenList}</div>
                        </div>
                        <div class="neem-contact">
                            <a href="javascript:void(0);" class="neem-contact-link">neem contact</a>
                            <div class="detail-price-info">€${listing.prijs}</div>
                        </div>
                    </div>
                `;
            }

            modal.style.display = 'block';
        })
        .catch(error => console.error('Error:', error));
}

function closeModal() {
    const indexModal = document.getElementById('detailsModal');
    const woningenModal = document.getElementById('woningenDetailsModal');
    if (indexModal) indexModal.style.display = 'none';
    if (woningenModal) woningenModal.style.display = 'none';
}

function closeModalAndNavigateToContact() {
    closeModal();
    navigateToSlide('contact');
}

function navigateToSlide(slideId) {
    document.getElementById(`nav${slideId.charAt(0).toUpperCase() + slideId.slice(1)}`).click();
}

document.addEventListener('DOMContentLoaded', function() {
    const closeButtons = document.querySelectorAll('.close-details-modal, .close-woningen-details-modal');
    closeButtons.forEach(btn => {
        btn.onclick = closeModal;
    });

    window.onclick = function(event) {
        const indexModal = document.getElementById('detailsModal');
        const woningenModal = document.getElementById('woningenDetailsModal');
        if ((indexModal && event.target == indexModal) || (woningenModal && event.target == woningenModal)) {
            closeModal();
        }
    };

    const listingsContainer = document.getElementById('listingsContainer');
    if (listingsContainer) {
        listingsContainer.addEventListener('click', function(event) {
            if (event.target && event.target.matches(".more-info")) {
                const listingId = event.target.getAttribute('data-id');
                const modalType = event.target.getAttribute('data-modal-type') || 'index';
                if (listingId) {
                    openDetailsModal(listingId, modalType);
                }
            }
        });
    }
});



function removeListing(listingId) {
    if (confirm("Are you sure you want to delete this listing?")) {
        fetch(`/woningmakelaar/assets/php/remove_listing.php?id=${listingId}`, { method: 'POST' })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(result => {
                if (result.success) {
                    alert("Listing removed successfully.");
                    window.location.reload();
                } else {
                    alert("Error removing listing.");
                }
            })
            .catch(error => {
                console.error('Error:', error);
                window.location.reload();
            });
    }
}
