document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Content Loaded');
    
    // Get all view details buttons
    const viewButtons = document.querySelectorAll('.lf-view-details-btn');
    console.log('Found view buttons:', viewButtons.length);
    
    // Log each button's attributes
    viewButtons.forEach((button, index) => {
        console.log(`Button ${index + 1} attributes:`, {
            transactionId: button.getAttribute('data-transaction-id'),
            itemName: button.getAttribute('data-item-name'),
            reportStatus: button.getAttribute('data-report-status'),
            responseStatus: button.getAttribute('data-response-status'),
            responseId: button.getAttribute('data-response-id')
        });
    });
    
    const closeButtons = document.querySelectorAll('#lf-close, #lf-close2, #lf-close-response, #lf-close-view-response');
    console.log('Found close buttons:', closeButtons.length);
    
    // Store the current transaction ID when opening the lost item modal
    let currentTransactionId = null;
    
    // Get claim button and log if it exists
    const claimButtons = document.querySelectorAll('#lf-mark-claimed');
    console.log('Found claim buttons:', claimButtons.length);
    
    // Log when claim button is found and add click handler to debug
    claimButtons.forEach(button => {
        console.log('Adding click handler to claim button');
        button.addEventListener('click', function() {
            console.log('Direct claim button click handler fired');
        });
    });
    
    // Handle close buttons
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
    
    viewButtons.forEach(button => {
        button.addEventListener('click', function() {
            console.log('View details button clicked');
            const transactionId = this.getAttribute('data-transaction-id');
            const reportStatus = this.getAttribute('data-report-status');
            console.log('Transaction ID:', transactionId);
            console.log('Report Status:', reportStatus);
            
            // Store transaction ID globally for Add Response
            currentTransactionId = transactionId;
            console.log('Set currentTransactionId to:', currentTransactionId);
            
            // Determine which modal to show
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
        
        const elements = {
            '.lf-item-name': data.itemName,
            '.lf-report-status': data.reportStatus,
            '.lf-category-name': data.categoryName,
            '.lf-found-location': data.foundLocation,
            '.lf-stored-location': data.storedLocation,
            '.lf-date-filed': data.dateFiled,
            '.lf-pub-id': data.pubName,
            '.lf-user-respo': data.userRespoName,
            '.lf-desc-content': data.itemDetails,
            '.lf-claim-status': data.claimStatus
        };
        
        // Update each element if it exists
        Object.entries(elements).forEach(([selector, value]) => {
            const element = modalContainer.querySelector(selector);
            if (element) element.textContent = value;
        });
        
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
            // Show View Response button only if responseStatus is exactly 'responded' (case-insensitive)
            const hasResponse = (status === 'responded');
            // All users can see the view response button if hasResponse
            if (addResponseBtnCont) addResponseBtnCont.style.display = 'none';
            if (viewResponseBtnCont) viewResponseBtnCont.style.display = hasResponse ? 'block' : 'none';

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
                        // Always use the latest data from the button attributes if available
                        viewModal.querySelector('#view-response-id').textContent = data.responseId || '';
                        viewModal.querySelector('#view-location-found').textContent = data.foundlocRespo || '';
                        viewModal.querySelector('#view-storage-location').textContent = data.storelocRespo || '';
                        viewModal.querySelector('#view-item-description').textContent = data.otherInfo || '';
                        viewModal.querySelector('#view-response-by').textContent = data.userRespoName || '';
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

        console.log('data.responseStatus:', data.responseStatus);
        console.log('status:', status);
        console.log('viewResponseBtnCont:', viewResponseBtnCont);
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
                    const viewDetailsBtn = document.querySelector(`.lf-view-details-btn[data-transaction-id="${transactionId}"]`);
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
});