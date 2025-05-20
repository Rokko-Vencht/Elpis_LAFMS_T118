document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Content Loaded');
    
    // FORM SUBMISSION VALIDATION - PREVENTS MULTIPLE SUBMISSIONS
    let isSubmitting = false;
    
    // Get all view details buttons
    const viewButtons = document.querySelectorAll('.lf-view-details-btn');
    console.log('Found view buttons:', viewButtons.length);
    
    const closeButtons = document.querySelectorAll('#lf-close, #lf-close2, #lf-close-response, #lf-close-view-response');
    console.log('Found close buttons:', closeButtons.length);
    


    // TRANSACTION ID VALIDATION - ENSURES TRANSACTION ID EXISTS
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
    

    // CLOSE BUTTON
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
            
            // TRANSACTION ID AND REPORT STATUS VALIDATION
            const transactionId = this.getAttribute('data-transaction-id');
            const reportStatus = this.getAttribute('data-report-status');
            console.log('Transaction ID:', transactionId);
            console.log('Report Status:', reportStatus);
            
            // Store transaction ID globally for Add Response
            currentTransactionId = transactionId;
            console.log('Set currentTransactionId to:', currentTransactionId);
            

            // MODAL CONTAINER VALIDATION
            let modalContainer;
            if (reportStatus === 'Lost') {
                modalContainer = document.getElementById('lf-modal-container');
                console.log('Lost modal found:', !!modalContainer);
            } else {
                modalContainer = document.getElementById('lf-modal-container-f');
                console.log('Found modal found:', !!modalContainer);
            }
            
            
            // MODAL EXISTENCE VALIDATION
            if (!modalContainer) {
                console.error('Modal container not found');
                return;
            }
            

            // Log the state before setting the transaction ID
            console.log('Modal container before setting transactionId:', {
                id: modalContainer.id,
                currentDataset: {...modalContainer.dataset}
            });

            // Attach the transaction ID to the modal as a data attribute for easy access
            modalContainer.dataset.transactionId = transactionId;
            
            // Log the state after setting the transaction ID
            console.log('Modal container after setting transactionId:', {
                id: modalContainer.id,
                currentDataset: {...modalContainer.dataset}
            });
            

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
        // CLAIM STATUS VALIDATION
        console.log('data.claimStatus:', data.claimStatus);
        
        // TRANSACTION ID VALIDATION FOR MODAL
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
            '.lf-claim-status': data.claimStatus,
            '.lf-transaction-status': data.transactionStatus
        };
        
        // Update each element if it exists
        Object.entries(elements).forEach(([selector, value]) => {
            const element = modalContainer.querySelector(selector);
            if (element) element.textContent = value;
        });
        
        // USER PERMISSION VALIDATION - CHECKS IF USER IS PUBLISHER
        const currentUserId = window.currentUserId;
        console.log('Current user ID:', currentUserId, 'Publisher ID:', data.pubId);
        
        const viewResponseBtnCont = modalContainer.querySelector('.lf-view-response-btn-cont');

        // LOST ITEM VALIDATION AND PERMISSIONS
        if (data.reportStatus === 'Lost') {
            const status = data.responseStatus ? data.responseStatus.trim().toLowerCase() : '';
            console.log('Lost item - response status:', status);
            
            // PUBLISHER PERMISSION VALIDATION FOR RESOLVE ACTION
            if (markResolvedBtn) {
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

        // FOUND ITEM VALIDATION - CLAIMED/RESOLVED LOGIC
        if (data.reportStatus === 'Found') {
            // CLAIM STATUS VALIDATION
            const isClaimed = data.claimStatus && data.claimStatus.toLowerCase() === 'claimed';
            const hasUserRespo = data.userRespo && data.userRespo !== '' && data.userRespo !== 'null';
            console.log('Found item - isClaimed:', isClaimed, 'hasUserRespo:', hasUserRespo);

            // Initially hide both buttons
            if (markClaimedBtn) markClaimedBtn.style.display = 'none';
            if (markResolvedBtn) markResolvedBtn.style.display = 'none';


            /**
             * PUBLISHER LOGIC
             * 
             * Streamlines which button to choose depending on the user's role in each item cards
             * 
             */

            if (currentUserId == data.pubId) {
                console.log('User is publisher');
                if (isClaimed && hasUserRespo) {

                    // PUBLISHER AND CLAIMED
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

                    // NARK AS CLAIMED: NOT PUBLISHER AND NOT CLAIMED
                    if (markClaimedBtn) {
                        markClaimedBtn.style.display = 'block';
                        console.log('Mark as Claimed button is shown');
                        
                        // Add direct event listener to the button
                        markClaimedBtn.onclick = function(e) {
                            console.log('Mark as Claimed button clicked directly!');
                            e.preventDefault();
                            
                            // USER CONFIRMATION VALIDATION
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

    // Function to show loading overlay
    function showLoading() {
        const loadingOverlay = document.getElementById('loading-overlay');
        if (loadingOverlay) {
            loadingOverlay.classList.add('show');
        }
    }

    // Function to hide loading overlay
    function hideLoading() {
        const loadingOverlay = document.getElementById('loading-overlay');
        if (loadingOverlay) {
            loadingOverlay.classList.remove('show');
        }
    }

    // RESPONSE FORM VALIDATION - PREVENTS EMPTY SUBMISSIONS
    const addResponseForm = document.querySelector('#lf-modal-container-response form');
    if (addResponseForm) {
        let isSubmitting = false;
        const submitButton = addResponseForm.querySelector('button[type="submit"]');

        addResponseForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            // DUPLICATE SUBMISSION PREVENTION
            if (isSubmitting) {
                return;
            }

            isSubmitting = true;
            if (submitButton) {
                submitButton.disabled = true;
            }
            showLoading();

            console.log('Form submitted');
            
            // REQUIRED FIELDS VALIDATION
            const foundlocRespo = document.getElementById('lf-foundloc-respo').value;
            const storelocRespo = document.getElementById('lf-storeloc-respo').value;
            const otherInfo = document.getElementById('lf-other-info').value;
            
            // TRANSACTION ID VALIDATION FOR RESPONSE
            const transactionId = currentTransactionId;
            if (!transactionId) {
                hideLoading();
                isSubmitting = false;
                if (submitButton) {
                    submitButton.disabled = false;
                }
                alert('Error: No transaction ID found');
                return;
            }

            console.log('Submitting response for transaction:', transactionId);

            try {
                const response = await fetch('view_add_response.php', {
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
                });

                console.log('Raw response:', response);
                const text = await response.text();
                
                try {
                    const data = JSON.parse(text);
                    console.log('Response data:', data);
                    
                    if (data.success) {
                        alert('Response submitted successfully!');
                        document.getElementById('lf-modal-container-response').classList.remove('show');
                        // Refresh the page to show updated data
                        window.location.reload();
                    } else {
                        alert('Error: ' + (data.error || 'Failed to submit response'));
                    }
                } catch (e) {
                    console.error('Error parsing JSON:', text);
                    throw new Error('Invalid JSON response from server');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error submitting response. Please try again.');
            } finally {
                hideLoading();
                isSubmitting = false;
                if (submitButton) {
                    submitButton.disabled = false;
                }
            }
        });
    }

    // CLAIM ACTION VALIDATION
    let isClaimSubmitting = false;
    document.addEventListener('click', async function(e) {
        if (e.target.closest('#lf-mark-claimed')) {
            // DUPLICATE CLAIM PREVENTION
            if (isClaimSubmitting) {
                return;
            }

            console.log('Mark as Claimed button clicked!');
            
            // Add detailed logging
            const claimButton = e.target.closest('#lf-mark-claimed');
            console.log('Claim button found:', !!claimButton);
            
            const modal = claimButton.closest('.lf-modal');
            console.log('Modal found:', !!modal);
            console.log('Modal dataset:', modal ? {...modal.dataset} : 'No modal');
            
            const modalTransactionId = modal ? modal.dataset.transactionId : null;
            console.log('Modal Transaction ID:', modalTransactionId);
            console.log('Current Transaction ID (global):', currentTransactionId);
            
            // Get the transaction ID from both possible sources
            const transactionId = modalTransactionId || currentTransactionId;
            console.log('Final Transaction ID to be used:', transactionId);
            
            // Add a small delay to allow for any race conditions
            console.log('Starting delay...');
            await new Promise(resolve => setTimeout(resolve, 100));
            console.log('Delay complete');
            
            if (!transactionId) {
                console.error('Could not find transaction ID from either source');
                console.error('Modal state at error:', {
                    modalExists: !!modal,
                    modalDataset: modal ? {...modal.dataset} : 'No modal',
                    modalTransactionId,
                    currentTransactionId,
                    buttonState: claimButton ? 'Found' : 'Not Found'
                });
                return;
            }

            console.log('Proceeding with claim for transaction:', transactionId);
            
            // USER CONFIRMATION VALIDATION
            if (!confirm('Are you sure you want to claim this item?')) {
                return;
            }

            isClaimSubmitting = true;
            claimButton.disabled = true;
            showLoading();

            try {
                // Send AJAX request to mark the item as claimed
                const response = await fetch('mark_as_claimed.php', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        transaction_id: transactionId
                    })
                });

                console.log('Raw response:', response);
                const text = await response.text();
                
                try {
                    const data = JSON.parse(text);
                    console.log('Response data:', data);
                    
                    if (data.success) {
                        alert('Item marked as claimed successfully!');
                        // Close the modal
                        const modalContainer = document.querySelector('.lf-modal-container-f.show');
                        if (modalContainer) {
                            modalContainer.classList.remove('show');
                            document.body.style.overflow = '';
                        }
                        // Refresh the page to show updated data
                        window.location.reload();
                    } else {
                        alert('Error: ' + (data.error || 'Failed to mark item as claimed'));
                    }
                } catch (e) {
                    console.error('Error parsing JSON:', text);
                    throw new Error('Invalid JSON response from server');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error marking item as claimed. Please try again.');
            } finally {
                hideLoading();
                isClaimSubmitting = false;
                claimButton.disabled = false;
            }
        }
    });


    // RESOLVE ACTION VALIDATION
    function handleMarkAsResolved(e, transactionId) {
        e.preventDefault();
        console.log('Mark as Resolved handler called with transaction ID:', transactionId);
        
        // TRANSACTION ID VALIDATION
        if (!transactionId) {
            console.error('No transaction ID provided to handleMarkAsResolved');
            alert('Error: Could not identify the item to resolve');
            return;
        }
        
        // USER CONFIRMATION VALIDATION
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

    // Add Response Form Submission Handler
    const responseForm = document.querySelector('.lf-modal-container-response form');
    if (responseForm) {
        responseForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (isSubmitting) return;
            isSubmitting = true;
            
            const foundLocRespo = document.getElementById('lf-foundloc-respo').value;
            const storeLocRespo = document.getElementById('lf-storeloc-respo').value;
            const otherInfo = document.getElementById('lf-other-info').value;
            
            if (!foundLocRespo || !storeLocRespo) {
                alert('Please fill in all required fields');
                isSubmitting = false;
                return;
            }
            
            const formData = new FormData();
            formData.append('transaction_id', currentTransactionId);
            formData.append('foundloc_respo', foundLocRespo);
            formData.append('storeloc_respo', storeLocRespo);
            formData.append('other_info', otherInfo);
            
            fetch('../includes/submit_response.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Response submitted successfully!');
                    // Close the modal
                    const modalContainer = document.querySelector('.lf-modal-container-response');
                    if (modalContainer) {
                        modalContainer.classList.remove('show');
                        document.body.style.overflow = '';
                    }
                    // Refresh the page to show updated status
                    window.location.reload();
                } else {
                    alert('Error submitting response: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while submitting the response');
            })
            .finally(() => {
                isSubmitting = false;
            });
        });
    }
});