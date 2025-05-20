document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Content Loaded');
    

    // Get all view details buttons
    const viewButtons = document.querySelectorAll('.lf-edit-details-btn');
    console.log('Found view buttons:', viewButtons.length);
    
    const closeButtons = document.querySelectorAll('#lf-close, #lf-close2, #lf-close-response, #lf-close-view-response');
    console.log('Found close buttons:', closeButtons.length);
    

    // Store the current transaction ID when opening the lost item modal
    let currentTransactionId = null;
   
    
    // Get claim button and log if it exists
    const claimButtons = document.querySelectorAll('#lf-mark-claimed');
    console.log('Found claim buttons:', claimButtons.length);
    

    
    claimButtons.forEach(button => {
        console.log('Adding click handler to claim button');
        button.addEventListener('click', function() {
            console.log('Direct claim button click handler fired');
        });
    });

    
    // Closes btns
    closeButtons.forEach(button => {
        button.addEventListener('click', function() {
            console.log('Close button clicked');
            const modalContainer = this.closest('.lf-modal-container, .lf-modal-container-f, .lf-modal-container-response, .lf-modal-container-view-response');
            if (modalContainer) {
                modalContainer.classList.remove('show');
                document.body.style.overflow = '';
                console.log('Modal hidden');
            }
        });
    });


    /**STREAMLINE THE LOST / FOUND MODALS */
    


    viewButtons.forEach(button => {
        button.addEventListener('click', function() {
            console.log('Edit details button clicked');
            const transactionId = this.getAttribute('data-transaction-id');
            const reportStatus = this.getAttribute('data-report-status');
            console.log('Transaction ID:', transactionId);
            console.log('Report Status:', reportStatus);
            

            // Store transaction ID globally for Add Response
            currentTransactionId = transactionId;
            console.log('Set currentTransactionId to:', currentTransactionId);
            

            // DetermineS the modal (is it Found or Lost)
            let modalContainer;
            if (reportStatus === 'Lost') {
                modalContainer = document.getElementById('lf-modal-container');
                console.log('Lost modal found:', !!modalContainer);
            } else {
                modalContainer = document.getElementById('lf-modal-container-f');
                console.log('Found modal found:', !!modalContainer);
            }
            
            if (!modalContainer) {
                console.error('Modal container not found');
                return;
            }
            

            // Attach the transaction ID to the modal as a data attribute for easy access
            modalContainer.dataset.transactionId = transactionId;
            

            // Get all the data attributes
            const itemData = {
                transactionId: transactionId,
                itemName: this.getAttribute('data-item-name'),
                itemImage: this.getAttribute('data-item-image'),
                itemDetails: this.getAttribute('data-item-details'),
                reportStatus: reportStatus,
                categoryName: this.getAttribute('data-category-name'),
                foundLocation: this.getAttribute('data-found-location'),
                storedLocation: this.getAttribute('data-stored-location'),
                dateFiled: this.getAttribute('data-date-filed'),
                pubName: this.getAttribute('data-pub-name'),
                pubId: this.getAttribute('data-pub-id'),
                userRespoName: this.getAttribute('data-user-respo-name'),
                claimStatus: this.getAttribute('data-claim-status'),
                userRespo: this.getAttribute('data-user-respo'),
                transactionStatus: this.getAttribute('data-transaction-status'),
                responseStatus: this.getAttribute('data-response-status'),
                responseId: this.getAttribute('data-response-id'),
                foundlocRespo: this.getAttribute('data-foundloc-respo'),
                storelocRespo: this.getAttribute('data-storeloc-respo'),
                otherInfo: this.getAttribute('data-other-info')
            };
            
            console.log('Item data:', itemData);
            

            // Update modal content
            updateModalContent(modalContainer, itemData);

            
            // Show the modal
            modalContainer.classList.add('show');
            document.body.style.overflow = 'hidden';
            console.log('Modal should be visible now');
            console.log('Modal classes:', modalContainer.className);
            console.log('Modal style:', window.getComputedStyle(modalContainer));
        });
    });
    
    function updateModalContent(modalContainer, data) {
        // Debug log for claim status
        console.log('data.claimStatus:', data.claimStatus);
        
        // Make sure the transaction ID is set on the modal
        if (data.transactionId) {
            modalContainer.dataset.transactionId = data.transactionId;
            console.log('Set transaction ID on modal:', data.transactionId);
        }
        
        // Update common fields
        const imgElement = modalContainer.querySelector('#lf-item-holder-img');
        if (imgElement) imgElement.src = data.itemImage;
        
        // Debug logs for buttons
        const markResolvedBtn = modalContainer.querySelector('#lf-mark-resolved');
        const markClaimedBtn = modalContainer.querySelector('#lf-mark-claimed');
        console.log('markResolvedBtn found:', !!markResolvedBtn);
        console.log('markClaimedBtn found:', !!markClaimedBtn);
        
        // Log all data attributes for debugging
        console.log('All item data for modal population:', data);
        
        // Update text elements
        const elements = {
            '.lf-report-status': data.reportStatus,
            '.lf-date-filed': data.dateFiled,
            '.lf-pub-id': data.pubName,
            '.lf-user-respo': data.userRespoName,
            '.lf-desc-content': data.itemDetails,
            '.lf-claim-status': data.claimStatus,
            '.lf-transaction-status': data.transactionStatus
        };
        
        // Update each element if it exists
        Object.entries(elements).forEach(([selector, value]) => {
            const element = modalContainer.querySelector(selector);
            if (element) {
                element.textContent = value || 'N/A';
                console.log(`Updated ${selector} with:`, value || 'N/A');
            }
        });
        
        // Different handling based on modal type
        if (data.reportStatus === 'Lost') {
            // Update edit item name input for lost items
            const itemNameInput = modalContainer.querySelector('#edit-item-name');
            if (itemNameInput) {
                itemNameInput.value = data.itemName || '';
                console.log('Updated lost item name input:', data.itemName);
            }
            
            // Update category dropdown for lost items
            const categorySelect = modalContainer.querySelector('#edit-item-category');
            updateCategoryDropdown(categorySelect, data.categoryName);
            
            // Update last seen location for lost items
            const locationSelect = modalContainer.querySelector('#edit-location-last-seen');
            if (locationSelect) {
                updateLocationDropdown(locationSelect, data.foundLocation);
            }
            
            // Update item details textarea for lost items
            const detailsTextarea = modalContainer.querySelector('#edit-item-details');
            if (detailsTextarea) {
                detailsTextarea.value = data.itemDetails || '';
                console.log('Updated lost item details textarea:', data.itemDetails);
            }
        } else {
            // For found items
            // Update edit item name input for found items
            const itemNameInput = modalContainer.querySelector('#edit-item-name-found');
            if (itemNameInput) {
                itemNameInput.value = data.itemName || '';
                console.log('Updated found item name input:', data.itemName);
            }
            
            // Update category dropdown for found items
            const categorySelect = modalContainer.querySelector('#edit-item-category-found');
            updateCategoryDropdown(categorySelect, data.categoryName);
            
            // Update found location dropdown
            const foundLocationSelect = modalContainer.querySelector('#edit-location-found');
            if (foundLocationSelect) {
                updateLocationDropdown(foundLocationSelect, data.foundLocation);
            }
            
            // Update stored location dropdown
            const storedLocationSelect = modalContainer.querySelector('#edit-location-stored-found');
            if (storedLocationSelect) {
                updateLocationDropdown(storedLocationSelect, data.storedLocation);
            }
            
            // Update item details textarea for found items
            const detailsTextarea = modalContainer.querySelector('#edit-item-details-found');
            if (detailsTextarea) {
                detailsTextarea.value = data.itemDetails || '';
                console.log('Updated found item details textarea:', data.itemDetails);
            }
        }
        
        // Use the global variable set in view.php
        const currentUserId = window.currentUserId;
        console.log('Current user ID:', currentUserId, 'Publisher ID:', data.pubId);
        
        const viewResponseBtnCont = modalContainer.querySelector('.lf-view-response-btn-cont');

        // LOST MODAL LOGIC
        if (data.reportStatus === 'Lost') {
            const status = data.responseStatus ? data.responseStatus.trim().toLowerCase() : '';
            console.log('Lost item - response status:', status);
            
            // Set up Mark as Resolved button for Lost items
            if (markResolvedBtn) {
                // Only publisher can resolve
                const canResolve = currentUserId == data.pubId;
                markResolvedBtn.style.display = canResolve ? 'block' : 'none';
                console.log('Lost item - Mark as Resolved button visibility:', canResolve ? 'visible' : 'hidden');
                
                // Set up direct click handler for this instance
                if (canResolve) {
                    console.log('Adding direct resolve click handler for Lost item');
                    markResolvedBtn.onclick = function(e) {
                        handleMarkAsResolved(e, data.transactionId);
                    };
                }
            }
            
            // Hide claim button for Lost items
            if (markClaimedBtn) markClaimedBtn.style.display = 'none';
            
            // Response buttons logic
            const addResponseBtnCont = modalContainer.querySelector('.lf-add-response-btn-cont');
            if (currentUserId == data.pubId) {
                // Publisher can't add responses
                if (addResponseBtnCont) addResponseBtnCont.style.display = 'none';
                if (viewResponseBtnCont) viewResponseBtnCont.style.display = 'none';
            } else {
                // Non-publisher response options
                if (addResponseBtnCont) addResponseBtnCont.style.display = (status === 'pending') ? 'block' : 'none';
                if (viewResponseBtnCont) viewResponseBtnCont.style.display = (status === 'responded') ? 'block' : 'none';
            }

            // Always wire up the View Response button if it is visible
            if (viewResponseBtnCont && viewResponseBtnCont.style.display === 'block') {
                const btn = viewResponseBtnCont.querySelector('#lf-view-response-btn');
                if (btn) {
                    console.log('Adding click handler to View Response button');
                    btn.onclick = function() {
                        console.log('View Response button clicked');
                        const viewModal = document.getElementById('lf-modal-container-view-response');
                        if (!viewModal) {
                            console.error('View response modal not found');
                            return;
                        }
                        
                        viewModal.querySelector('#view-response-id').textContent = data.responseId;
                        viewModal.querySelector('#view-location-found').textContent = data.foundlocRespo;
                        viewModal.querySelector('#view-storage-location').textContent = data.storelocRespo;
                        viewModal.querySelector('#view-item-description').textContent = data.otherInfo;
                        viewModal.querySelector('#view-response-by').textContent = data.userRespoName;
                        
                        viewModal.classList.add('show');
                        document.body.style.overflow = 'hidden';
                    };
                }
            }
        }



        // FOUND MODAL: Claimed/Resolved logic
        if (data.reportStatus === 'Found') {
            // Normalize claim status and userRespo
            const isClaimed = data.claimStatus && data.claimStatus.toLowerCase() === 'claimed';
            const hasUserRespo = data.userRespo && data.userRespo !== '' && data.userRespo !== 'null';
            console.log('Found item - isClaimed:', isClaimed, 'hasUserRespo:', hasUserRespo);

            // Initially hide both buttons
            if (markClaimedBtn) markClaimedBtn.style.display = 'none';
            if (markResolvedBtn) markResolvedBtn.style.display = 'none';

            // Publisher logic
            if (currentUserId == data.pubId) {
                console.log('User is publisher');
                if (isClaimed && hasUserRespo) {
                    // Publisher & claimed: show Mark as Resolved
                    if (markResolvedBtn) {
                        markResolvedBtn.style.display = 'block';
                        console.log('Found item - Mark as Resolved button visible for publisher of claimed item');
                        
                        // Set up direct click handler for this instance
                        console.log('Adding direct resolve click handler for Found item');
                        markResolvedBtn.onclick = function(e) {
                            handleMarkAsResolved(e, data.transactionId);
                        };
                    }
                }
            } else {

                /**VALIDATION */

                console.log('User is not publisher');
                // Non-publisher logic

                if (!isClaimed && !hasUserRespo) {
                    // Not publisher & not claimed: show Mark as Claimed
                    if (markClaimedBtn) {
                        markClaimedBtn.style.display = 'block';
                        console.log('Mark as Claimed button is shown');
                        
                        // Add direct event listener to the button
                        markClaimedBtn.onclick = function(e) {
                            console.log('Mark as Claimed button clicked directly!');
                            e.preventDefault();
                            
                            if (!confirm('Are you sure you want to claim this item?')) {
                                return;
                            }
                            
                            console.log('Confirmed: Marking transaction as claimed:', data.transactionId);
                            
                            fetch('mark_as_claimed.php', {
                                method: 'POST',
                                headers: { 
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({
                                    transaction_id: data.transactionId
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    alert('Item marked as claimed successfully!');
                                    window.location.reload();
                                } else {
                                    alert('Error: ' + (data.error || 'Failed to mark item as claimed'));
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                alert('Error marking item as claimed. Please try again.');
                            });
                        };
                    }
                }
            }
        }
        
        // Fetch and update response data
        fetchResponseData(data.transactionId);
    }


    
    function fetchResponseData(transactionId) {
        // Make an AJAX call to get response data
        console.log('Fetching response data for transaction:', transactionId);
        fetch(`get_response_data.php?transaction_id=${transactionId}`)
            .then(response => {
                console.log('Raw fetch response:', response);
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.text().then(text => {
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        console.error('Error parsing JSON:', text);
                        throw new Error('Invalid JSON response from server');
                    }
                });
            })
            .then(data => {
                console.log('Response data received:', data);
                if (data.response) {
                    // Update the modal data attributes for the current transaction
                    const viewDetailsBtn = document.querySelector(`.lf-edit-details-btn[data-transaction-id="${transactionId}"]`);
                    if (viewDetailsBtn) {
                        viewDetailsBtn.setAttribute('data-response-id', data.response.response_id);
                        viewDetailsBtn.setAttribute('data-foundloc-respo', data.response.foundloc_respo);
                        viewDetailsBtn.setAttribute('data-storeloc-respo', data.response.storeloc_respo);
                        viewDetailsBtn.setAttribute('data-other-info', data.response.other_info);
                        viewDetailsBtn.setAttribute('data-user-respo-name', data.response.user_respo);
                        
                        console.log('Updated view details button with response data');
                    }
                    
                    // Update view response modal content directly
                    try {
                        const viewResponseId = document.getElementById('view-response-id');
                        const viewResponseBy = document.getElementById('view-response-by');
                        const viewLocationFound = document.getElementById('view-location-found');
                        const viewStorageLocation = document.getElementById('view-storage-location');
                        const viewItemDescription = document.getElementById('view-item-description');
                        
                        if (viewResponseId) viewResponseId.textContent = data.response.response_id;
                        if (viewResponseBy) viewResponseBy.textContent = data.response.user_respo;
                        if (viewLocationFound) viewLocationFound.textContent = data.response.foundloc_respo;
                        if (viewStorageLocation) viewStorageLocation.textContent = data.response.storeloc_respo;
                        if (viewItemDescription) viewItemDescription.textContent = data.response.other_info;
                        
                        console.log('Updated view response modal content');
                    } catch (e) {
                        console.error('Error updating view response modal:', e);
                    }
                    
                    // Show the View Response button if there is a response
                    const modalContainer = document.querySelector('.lf-modal-container.show, .lf-modal-container-f.show');
                    if (modalContainer) {
                        const viewResponseBtnCont = modalContainer.querySelector('.lf-view-response-btn-cont');
                        if (viewResponseBtnCont) {
                            viewResponseBtnCont.style.display = 'block';
                            console.log('View Response button container shown');
                        }
                    }
                }
            })
            .catch(error => console.error('Error fetching response data:', error));
    }
    
    // Additional handler for view response button (direct click)
    document.addEventListener('click', function(e) {
        const viewResponseBtn = e.target.closest('#lf-view-response-btn');
        if (viewResponseBtn) {
            console.log('View Response button clicked (global handler)');
            const viewModal = document.getElementById('lf-modal-container-view-response');
            if (viewModal) {
                const transactionId = currentTransactionId;
                console.log('Current transaction ID:', transactionId);
                
                // Ensure we have the latest data by fetching it again
                fetch(`get_response_data.php?transaction_id=${transactionId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.response) {
                            document.getElementById('view-response-id').textContent = data.response.response_id;
                            document.getElementById('view-response-by').textContent = data.response.user_respo;
                            document.getElementById('view-location-found').textContent = data.response.foundloc_respo;
                            document.getElementById('view-storage-location').textContent = data.response.storeloc_respo;
                            document.getElementById('view-item-description').textContent = data.response.other_info;
                            
                            viewModal.classList.add('show');
                            document.body.style.overflow = 'hidden';
                        } else {
                            alert('No response data found for this transaction.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error fetching response data. Please try again.');
                    });
            }
        }
    });
    
    // Handle add response button click
    document.addEventListener('click', function(e) {
        if (e.target.closest('#lf-add-report')) {
            console.log('Add Response button clicked!');
            const responseModal = document.getElementById('lf-modal-container-response');
            if (responseModal) {
                responseModal.classList.add('show');
                document.body.style.overflow = 'hidden';
            }
        }
    });

    // Add AJAX submit logic for Add Response form
    const addResponseForm = document.querySelector('#lf-modal-container-response form');
    if (addResponseForm) {
        addResponseForm.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('Form submitted');
            
            const foundlocRespo = document.getElementById('lf-foundloc-respo').value;
            const storelocRespo = document.getElementById('lf-storeloc-respo').value;
            const otherInfo = document.getElementById('lf-other-info').value;
            
            // Get the current transaction ID from the global variable
            const transactionId = currentTransactionId;
            
            if (!transactionId) {
                alert('Error: No transaction ID found');
                return;
            }

            console.log('Submitting response for transaction:', transactionId);
            console.log('Response data:', {
                foundloc_respo: foundlocRespo,
                storeloc_respo: storelocRespo,
                other_info: otherInfo,
                transaction_id: transactionId
            });

            fetch('view_add_response.php', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    foundloc_respo: foundlocRespo,
                    storeloc_respo: storelocRespo,
                    other_info: otherInfo,
                    transaction_id: transactionId
                })
            })
            .then(response => {
                console.log('Raw response:', response);
                return response.text().then(text => {
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        console.error('Error parsing JSON:', text);
                        throw new Error('Invalid JSON response from server');
                    }
                });
            })
            .then(data => {
                console.log('Response data:', data);
                if (data.success) {
                    alert('Response submitted successfully!');
                    document.getElementById('lf-modal-container-response').classList.remove('show');
                    // Refresh the page to show updated data
                    window.location.reload();
                } else {
                    alert('Error: ' + (data.error || 'Failed to submit response'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error submitting response. Please try again.');
            });
        });
    }

    // Add direct event listener for mark as claimed button
    document.addEventListener('DOMContentLoaded', function() {
        // Direct event handler for mark as claimed button to ensure it works
        document.querySelector('#lf-modal-container-f').addEventListener('click', function(e) {
            if (e.target.id === 'lf-mark-claimed' || e.target.closest('#lf-mark-claimed')) {
                e.preventDefault();
                console.log('Mark as Claimed button clicked via direct event delegation!');
                
                // Get transaction ID from the modal's data attribute
                const transactionId = this.dataset.transactionId || currentTransactionId;
                
                if (!transactionId) {
                    console.error('Error: No transaction ID found for claim button');
                    alert('Error: No transaction ID found for this item');
                    return;
                }
                
                console.log('Using transaction ID for claim:', transactionId);
                
                // Confirm with the user
                if (!confirm('Are you sure you want to claim this item?')) {
                    return;
                }
                
                // Send AJAX request to mark the item as claimed
                fetch('mark_as_claimed.php', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        transaction_id: transactionId
                    })
                })
                .then(response => {
                    console.log('Raw response from server:', response);
                    return response.text().then(text => {
                        console.log('Response text:', text);
                        try {
                            return JSON.parse(text);
                        } catch (e) {
                            console.error('Error parsing JSON:', e, text);
                            throw new Error('Invalid JSON response from server');
                        }
                    });
                })
                .then(data => {
                    console.log('Processed response data:', data);
                    if (data.success) {
                        alert('Item marked as claimed successfully!');
                        window.location.reload();
                    } else {
                        alert('Error: ' + (data.error || 'Failed to mark item as claimed'));
                    }
                })
                .catch(error => {
                    console.error('Error in fetch operation:', error);
                    alert('Error processing your request. Please try again.');
                });
            }
        });
    });

    // Handler for Mark as Resolved button
    function handleMarkAsResolved(e, transactionId) {
        e.preventDefault();
        console.log('Mark as Resolved handler called with transaction ID:', transactionId);
        
        if (!transactionId) {
            console.error('No transaction ID provided to handleMarkAsResolved');
            alert('Error: Could not identify the item to resolve');
            return;
        }
        
        // Confirm with the user
        if (!confirm('Are you sure you want to mark this item as resolved? This action cannot be undone.')) {
            return;
        }
        
        console.log('Sending resolve request for transaction:', transactionId);
        
        // Send AJAX request to mark the item as resolved
        fetch('mark_as_resolved.php', {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                transaction_id: transactionId
            })
        })
        .then(response => {
            console.log('Response status:', response.status);
            return response.text().then(text => {
                console.log('Response text:', text);
                try {
                    return JSON.parse(text);
                } catch (e) {
                    console.error('Parse error:', e);
                    throw new Error('Invalid response: ' + text);
                }
            });
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success) {
                alert('Item marked as resolved successfully!');
                window.location.reload();
            } else {
                alert('Error: ' + (data.error || 'Failed to mark item as resolved'));
            }
        })
        .catch(error => {
            console.error('Request error:', error);
            alert('Error: ' + error.message);
        });
    }

    // Handler for Save Changes button
    document.addEventListener('click', function(e) {
        if (e.target.id === 'lf-save-changes') {
            e.preventDefault();
            console.log('Save Changes button clicked');
            
            // Get the modal container
            const modalContainer = e.target.closest('.lf-modal-container, .lf-modal-container-f');
            if (!modalContainer) {
                console.error('Modal container not found');
                return;
            }
            
            // Get the transaction ID
            const transactionId = modalContainer.dataset.transactionId;
            if (!transactionId) {
                console.error('No transaction ID found for this item');
                alert('Error: No transaction ID found for this item');
                return;
            }
            
            // Determine if this is a lost or found item
            const reportStatus = modalContainer.querySelector('.lf-report-status').textContent;
            const isLostItem = reportStatus === 'Lost';
            
            // Get the updated values based on which modal is open
            let itemName, categoryId, foundLocationId, storedLocationId, itemDetails;
            
            if (isLostItem) {
                itemName = modalContainer.querySelector('#edit-item-name').value;
                categoryId = modalContainer.querySelector('#edit-item-category').value;
                foundLocationId = modalContainer.querySelector('#edit-location-last-seen').value;
                itemDetails = modalContainer.querySelector('#edit-item-details').value;
            } else {
                itemName = modalContainer.querySelector('#edit-item-name-found').value;
                categoryId = modalContainer.querySelector('#edit-item-category-found').value;
                foundLocationId = modalContainer.querySelector('#edit-location-found').value;
                storedLocationId = modalContainer.querySelector('#edit-location-stored-found').value;
                itemDetails = modalContainer.querySelector('#edit-item-details-found').value;
            }
            
            // Validate the form
            if (!itemName || !categoryId) {
                alert('Please fill in all required fields');
                return;
            }
            
            // For found items, both locations are required
            if (!isLostItem && (!foundLocationId || !storedLocationId)) {
                alert('Please select both found and stored locations');
                return;
            }
            
            // Confirm with the user
            if (!confirm('Are you sure you want to save these changes?')) {
                return;
            }
            
            // Prepare data for AJAX request
            const data = {
                transaction_id: transactionId,
                item_name: itemName,
                category_id: categoryId,
                report_status: reportStatus,
                item_details: itemDetails
            };
            
            // Add location fields based on report status
            if (isLostItem) {
                data.location_id = foundLocationId;
            } else {
                data.found_location_id = foundLocationId;
                data.stored_location_id = storedLocationId;
            }
            
            console.log('Sending update data:', data);
            



            // Send AJAX request to update the item
            fetch('update_item.php', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            })

            .then(response => {
                console.log('Raw response from server:', response);
                return response.text().then(text => {
                    console.log('Response text:', text);
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        console.error('Error parsing JSON:', e, text);
                        throw new Error('Invalid JSON response from server');
                    }
                });
            })
            .then(data => {
                console.log('Processed response data:', data);
                if (data.success) {
                    alert('Item updated successfully!');
                    window.location.reload();
                } else {
                    alert('Error: ' + (data.error || 'Failed to update item'));
                }
            })
            .catch(error => {
                console.error('Error in fetch operation:', error);
                alert('Error updating item. Please try again.');
            });
        }
    });



    // Helper functioN

    function updateCategoryDropdown(categorySelect, categoryName) {
        if (!categorySelect || !categoryName) return;
        
        console.log('Updating category select with name:', categoryName);
        
        // Try to find option by text first
        let found = false;
        Array.from(categorySelect.options).forEach(option => {
            if (option.text === categoryName) {
                option.selected = true;
                found = true;
                console.log('Found and selected category by text:', categoryName);
            }
        });
        
        if (!found) {
            const categoryMap = {
                'Electronics': 'CAT001',
                'Sports/Recreation': 'CAT002',
                'Documents': 'CAT003',
                'Personal Care/Items': 'CAT004',
                'Education': 'CAT005',
                'Academe': 'CAT006',
                'Jewelry': 'CAT007',
                'Cash': 'CAT008',
                'Perishables (Food and Items)': 'CAT009',
                'Furnitures': 'CAT010',
                'Others': 'CAT011'
            };
            
            const categoryValue = categoryMap[categoryName];
            if (categoryValue) {
                categorySelect.value = categoryValue;
                console.log('Selected category by mapping:', categoryName, '→', categoryValue);
            }
        }
    }
    

    // Helper function
    function updateLocationDropdown(locationSelect, locationName) {
        if (!locationSelect || !locationName) return;
        
        console.log('Updating location select with name:', locationName);
        
        // Try to find option by text first
        let found = false;
        Array.from(locationSelect.options).forEach(option => {
            if (option.text === locationName) {
                option.selected = true;
                found = true;
                console.log('Found and selected location by text:', locationName);
            }
        });
        

        // If not found, set the first non-disabled option as a fallback
        if (!found) {
            console.log('Location not found in dropdown, using fallback');
            for (let i = 0; i < locationSelect.options.length; i++) {
                if (!locationSelect.options[i].disabled) {
                    locationSelect.options[i].selected = true;
                    break;
                }
            }
        }
    }
});