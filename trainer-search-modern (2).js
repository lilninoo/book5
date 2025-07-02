/**
 * JavaScript COMPLET TRAINER REGISTRATION PRO - VERSION CORRIGÉE
 * 
 * Contient :
 * ✅ Formulaire d'inscription modernisé (multi-étapes, validation temps réel)
 * ✅ Recherche de formateurs avec AJAX
 * ✅ Cartes de formateurs interactives
 * ✅ Upload de fichiers avec drag & drop
 * ✅ Animations et UX optimisées
 * ✅ Système d'erreurs avancé avec scroll automatique
 * ✅ Validation en temps réel style Stripe
 * ✅ NOUVEAU : Validation des régions d'intervention
 * ✅ NOUVEAU : LinkedIn optionnel
 * ✅ NOUVEAU : Gestion des profils anonymisés
 * 
 * Version: 2.1 - Fonctionnalités complètes
 */

(function($) {
    'use strict';

    // ===== VÉRIFICATIONS INITIALES =====
    if (typeof $ === 'undefined') {
        console.error('❌ Trainer Registration Pro: jQuery non trouvé');
        return;
    }

    if (typeof trainer_ajax === 'undefined') {
        console.error('❌ Trainer Registration Pro: Configuration AJAX manquante');
        return;
    }

    $(document).ready(function() {
        console.log('🚀 Trainer Registration Pro: Initialisation complète...');

        // ===== VARIABLES GLOBALES =====
        let currentStep = 1;
        const totalSteps = 4;
        let formSubmitting = false;
        let validationTimeout = null;
        let searchTimeout;
        
        // Cache des éléments DOM
        const elements = {
            form: $('#trpro-trainer-registration-form'),
            steps: $('.trpro-form-step'),
            progressSteps: $('.trpro-progress-step'),
            nextBtn: $('#trpro-next-step'),
            prevBtn: $('#trpro-prev-step'),
            submitBtn: $('#trpro-submit-form'),
            messages: $('#trpro-form-messages'),
            loading: $('#trpro-form-loading')
        };

        // ===== INITIALISATION GLOBALE =====
        
        // Formulaire d'inscription
        if (elements.form.length > 0) {
            initFormNavigation();
            initRealTimeValidation();
            initFileUpload();
            initCheckboxes();
            initFormAnimations();
            initRegionsValidation(); // ✅ NOUVEAU
            showStep(1);
            console.log('✅ Formulaire d\'inscription initialisé avec régions');
        }

        // Recherche de formateurs
        if ($('#trpro-trainer-search').length > 0) {
            initSearch();
            console.log('✅ Recherche de formateurs initialisée');
        }

        // Cartes de formateurs
        if ($('.trpro-trainer-card').length > 0) {
            initTrainerCards();
            console.log('✅ Cartes de formateurs initialisées');
        }

        // Profils détaillés
        initProfileModals(); // ✅ NOUVEAU

        // Animations générales
        initGlobalAnimations();

        // ===== FORMULAIRE D'INSCRIPTION - NAVIGATION MULTI-ÉTAPES MODERNE =====
        
        function initFormNavigation() {
            // Événements des boutons
            elements.nextBtn.on('click', handleNextStep);
            elements.prevBtn.on('click', handlePrevStep);
            elements.submitBtn.on('click', handleSubmit);
            elements.form.on('submit', function(e) {
                e.preventDefault();
                return false;
            });

            // Navigation clavier
            $(document).on('keydown', function(e) {
                if (elements.form.is(':visible') && e.key === 'Enter' && !e.shiftKey) {
                    const activeElement = document.activeElement;
                    if (activeElement.tagName !== 'TEXTAREA') {
                        e.preventDefault();
                        if (currentStep < totalSteps) {
                            handleNextStep();
                        } else {
                            handleSubmit();
                        }
                    }
                }
            });
        }

        function handleNextStep() {
            console.log(`🔄 Tentative passage à l'étape ${currentStep + 1}`);
            
            if (validateCurrentStep()) {
                if (currentStep < totalSteps) {
                    currentStep++;
                    showStep(currentStep);
                    console.log(`✅ Passage réussi à l'étape ${currentStep}`);
                }
            } else {
                console.log(`❌ Validation échouée pour l'étape ${currentStep}`);
            }
        }

        function handlePrevStep() {
            if (currentStep > 1) {
                currentStep--;
                showStep(currentStep);
                console.log(`⬅️ Retour à l'étape ${currentStep}`);
            }
        }

        function handleSubmit() {
            console.log('📤 Tentative de soumission...');
            
            if (formSubmitting) {
                console.log('⚠️ Soumission déjà en cours');
                return;
            }
            
            if (validateCurrentStep()) {
                submitForm();
            }
        }

        function showStep(step) {
            console.log(`📄 Affichage étape ${step}`);
            
            // Masquer toutes les étapes
            elements.steps.removeClass('active').hide();
            
            // Afficher l'étape courante avec animation
            const $currentStep = $(`.trpro-form-step[data-step="${step}"]`);
            $currentStep.addClass('active').fadeIn(300);
            
            // Mise à jour de la barre de progression
            updateProgressBar(step);
            
            // Gestion des boutons
            elements.prevBtn.toggle(step > 1);
            
            if (step === totalSteps) {
                elements.nextBtn.hide();
                elements.submitBtn.show();
                generateSummary();
            } else {
                elements.nextBtn.show();
                elements.submitBtn.hide();
            }
            
            // Scroll fluide vers le formulaire
            scrollToForm();
            
            // Focus sur le premier champ
            setTimeout(() => {
                $currentStep.find('input, textarea, select').first().focus();
            }, 350);
        }

        function updateProgressBar(step) {
            elements.progressSteps.removeClass('active completed');
            
            for (let i = 1; i <= step; i++) {
                $(`.trpro-progress-step[data-step="${i}"]`).addClass('active');
            }
            
            for (let i = 1; i < step; i++) {
                $(`.trpro-progress-step[data-step="${i}"]`).addClass('completed');
            }
        }

        function scrollToForm() {
            const container = $('.trpro-registration-container');
            if (container.length > 0) {
                $('html, body').animate({
                    scrollTop: container.offset().top - 100
                }, 400, 'easeOutCubic');
            }
        }

        // ===== VALIDATION MODERNE EN TEMPS RÉEL =====
        
        function initRealTimeValidation() {
            // Validation pendant la saisie (debounced)
            elements.form.find('input, textarea, select').on('input', function() {
                const $field = $(this);
                clearTimeout(validationTimeout);
                validationTimeout = setTimeout(() => {
                    validateField($field);
                }, 300);
            });

            // Validation à la perte de focus
            elements.form.find('input, textarea, select').on('blur', function() {
                validateField($(this));
            });

            // Validation des checkboxes
            elements.form.find('input[type="checkbox"]').on('change', function() {
                const $field = $(this);
                const name = $field.attr('name');
                
                if (name === 'specialties[]') {
                    validateSpecialties();
                } else if (name === 'intervention_regions[]') {
                    validateRegions(); // ✅ NOUVEAU
                } else if (name === 'rgpd_consent') {
                    validateRgpd();
                }
            });
        }

        // ===== NOUVEAU : VALIDATION DES RÉGIONS D'INTERVENTION =====
        
        function initRegionsValidation() {
            // Validation en temps réel des régions
            $('input[name="intervention_regions[]"]').on('change', function() {
                validateRegions();
                updateRegionsCounter();
            });
            
            // Compteur visuel
            updateRegionsCounter();
        }

        function validateRegions() {
            const $checked = $('input[name="intervention_regions[]"]:checked');
            const $container = $('.trpro-regions-grid');
            const $errorMsg = $('#trpro-regions-error');
            
            $container.removeClass('trpro-error-highlight');
            $errorMsg.text('');
            
            if ($checked.length === 0) {
                $container.addClass('trpro-error-highlight');
                $errorMsg.text('Veuillez sélectionner au moins une zone d\'intervention');
                return false;
            }
            
            // Suggestion si trop de régions sélectionnées
            if ($checked.length > 8) {
                $errorMsg.text('Conseil : Sélectionnez vos zones principales pour une meilleure visibilité').css('color', '#f59e0b');
                return true; // Pas bloquant, juste un conseil
            }
            
            return true;
        }

        function updateRegionsCounter() {
            const $checked = $('input[name="intervention_regions[]"]:checked');
            const count = $checked.length;
            
            // Créer ou mettre à jour le compteur
            let $counter = $('.trpro-regions-counter');
            if ($counter.length === 0) {
                $counter = $('<div class="trpro-regions-counter"></div>');
                $('.trpro-regions-grid').after($counter);
            }
            
            if (count > 0) {
                const text = count === 1 ? '1 zone sélectionnée' : `${count} zones sélectionnées`;
                const emoji = count <= 3 ? '📍' : count <= 6 ? '🗺️' : '🌍';
                $counter.html(`<span class="trpro-counter-text">${emoji} ${text}</span>`).show();
            } else {
                $counter.hide();
            }
        }

        function validateCurrentStep() {
            const $currentStepElement = $(`.trpro-form-step[data-step="${currentStep}"]`);
            const errors = [];
            
            console.log(`🔍 Validation étape ${currentStep}`);
            
            // Nettoyer les erreurs précédentes
            clearStepErrors();
            
            // Validation selon l'étape
            switch (currentStep) {
                case 1:
                    errors.push(...validateStep1($currentStepElement));
                    break;
                case 2:
                    errors.push(...validateStep2($currentStepElement));
                    break;
                case 3:
                    errors.push(...validateStep3($currentStepElement));
                    break;
                case 4:
                    errors.push(...validateStep4($currentStepElement));
                    break;
            }
            
            if (errors.length > 0) {
                displayErrors(errors);
                scrollToFirstError();
                return false;
            }
            
            return true;
        }

        // ===== VALIDATIONS PAR ÉTAPE CORRIGÉES =====
        
        function validateStep1($step) {
            const errors = [];
            
            // Prénom
            const firstName = $step.find('#trpro-first-name').val().trim();
            if (!firstName) {
                errors.push({
                    field: 'first_name',
                    selector: '#trpro-first-name',
                    message: 'Le prénom est obligatoire'
                });
            } else if (firstName.length < 2) {
                errors.push({
                    field: 'first_name',
                    selector: '#trpro-first-name',
                    message: 'Le prénom doit contenir au moins 2 caractères'
                });
            } else if (!/^[a-zA-ZÀ-ÿ\s-']+$/.test(firstName)) {
                errors.push({
                    field: 'first_name',
                    selector: '#trpro-first-name',
                    message: 'Le prénom contient des caractères non autorisés'
                });
            }
            
            // Nom
            const lastName = $step.find('#trpro-last-name').val().trim();
            if (!lastName) {
                errors.push({
                    field: 'last_name',
                    selector: '#trpro-last-name',
                    message: 'Le nom est obligatoire'
                });
            } else if (lastName.length < 2) {
                errors.push({
                    field: 'last_name',
                    selector: '#trpro-last-name',
                    message: 'Le nom doit contenir au moins 2 caractères'
                });
            } else if (!/^[a-zA-ZÀ-ÿ\s-']+$/.test(lastName)) {
                errors.push({
                    field: 'last_name',
                    selector: '#trpro-last-name',
                    message: 'Le nom contient des caractères non autorisés'
                });
            }
            
            // Email
            const email = $step.find('#trpro-email').val().trim();
            if (!email) {
                errors.push({
                    field: 'email',
                    selector: '#trpro-email',
                    message: 'L\'adresse email est obligatoire'
                });
            } else if (!isValidEmail(email)) {
                errors.push({
                    field: 'email',
                    selector: '#trpro-email',
                    message: 'Format d\'email invalide'
                });
            }
            
            // Téléphone
            const phone = $step.find('#trpro-phone').val().trim();
            if (!phone) {
                errors.push({
                    field: 'phone',
                    selector: '#trpro-phone',
                    message: 'Le numéro de téléphone est obligatoire'
                });
            } else if (!isValidPhone(phone)) {
                errors.push({
                    field: 'phone',
                    selector: '#trpro-phone',
                    message: 'Format de téléphone invalide (format français attendu)'
                });
            }
            
            // ✅ CORRIGÉ : LinkedIn optionnel - seulement valider si rempli
            const linkedin = $step.find('#trpro-linkedin-url').val().trim();
            if (linkedin && !linkedin.includes('linkedin.com')) {
                errors.push({
                    field: 'linkedin_url',
                    selector: '#trpro-linkedin-url',
                    message: 'URL LinkedIn invalide (doit contenir linkedin.com)'
                });
            }
            
            return errors;
        }

        function validateStep2($step) {
            const errors = [];
            
            // Spécialités
            const $specialties = $step.find('input[name="specialties[]"]:checked');
            if ($specialties.length === 0) {
                errors.push({
                    field: 'specialties',
                    selector: '.trpro-checkbox-grid',
                    message: 'Sélectionnez au moins une spécialité'
                });
            } else if ($specialties.length > 5) {
                errors.push({
                    field: 'specialties',
                    selector: '.trpro-checkbox-grid',
                    message: 'Maximum 5 spécialités recommandées'
                });
            }
            
            // ✅ NOUVEAU : Validation des régions d'intervention
            const $regions = $step.find('input[name="intervention_regions[]"]:checked');
            if ($regions.length === 0) {
                errors.push({
                    field: 'intervention_regions',
                    selector: '.trpro-regions-grid',
                    message: 'Sélectionnez au moins une zone d\'intervention'
                });
            }
            
            // Expérience
            const experience = $step.find('#trpro-experience').val().trim();
            if (!experience) {
                errors.push({
                    field: 'experience',
                    selector: '#trpro-experience',
                    message: 'Description de l\'expérience obligatoire'
                });
            } else if (experience.length < 50) {
                errors.push({
                    field: 'experience',
                    selector: '#trpro-experience',
                    message: `Description trop courte (${experience.length}/50 caractères minimum)`
                });
            } else if (experience.length > 1000) {
                errors.push({
                    field: 'experience',
                    selector: '#trpro-experience',
                    message: `Description trop longue (${experience.length}/1000 caractères maximum)`
                });
            }
            
            return errors;
        }

        function validateStep3($step) {
            const errors = [];
            
            // CV obligatoire
            const cvFile = $step.find('#trpro-cv-file')[0].files[0];
            if (!cvFile) {
                errors.push({
                    field: 'cv_file',
                    selector: '#trpro-cv-file',
                    message: 'Le CV est obligatoire'
                });
            } else {
                // Vérification taille
                if (cvFile.size > 5 * 1024 * 1024) {
                    errors.push({
                        field: 'cv_file',
                        selector: '#trpro-cv-file',
                        message: `CV trop volumineux (${formatFileSize(cvFile.size)}). Maximum: 5MB`
                    });
                }
                
                // Vérification type
                const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
                if (!allowedTypes.includes(cvFile.type)) {
                    errors.push({
                        field: 'cv_file',
                        selector: '#trpro-cv-file',
                        message: 'Format de CV non supporté. Utilisez PDF, DOC ou DOCX'
                    });
                }
            }
            
            // Photo (optionnelle mais si présente, doit être valide)
            const photoFile = $step.find('#trpro-photo-file')[0].files[0];
            if (photoFile) {
                if (photoFile.size > 2 * 1024 * 1024) {
                    errors.push({
                        field: 'photo_file',
                        selector: '#trpro-photo-file',
                        message: `Photo trop volumineuse (${formatFileSize(photoFile.size)}). Maximum: 2MB`
                    });
                }
                
                const allowedPhotoTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                if (!allowedPhotoTypes.includes(photoFile.type)) {
                    errors.push({
                        field: 'photo_file',
                        selector: '#trpro-photo-file',
                        message: 'Format de photo non supporté. Utilisez JPG, PNG ou GIF'
                    });
                }
            }
            
            return errors;
        }

        function validateStep4($step) {
            const errors = [];
            
            // Consentement RGPD obligatoire
            const rgpdConsent = $step.find('#trpro-rgpd-consent').prop('checked');
            if (!rgpdConsent) {
                errors.push({
                    field: 'rgpd_consent',
                    selector: '#trpro-rgpd-consent',
                    message: 'Le consentement RGPD est obligatoire'
                });
            }
            
            return errors;
        }

        // ===== VALIDATION INDIVIDUELLE DES CHAMPS =====
        
        function validateField($field) {
            const fieldName = $field.attr('name');
            const value = $field.val().trim();
            const $formGroup = $field.closest('.trpro-form-group');
            const $errorMsg = $field.siblings('.trpro-error-message');
            
            // Reset de l'état
            $formGroup.removeClass('error success');
            $errorMsg.text('').css('opacity', 0);
            
            let isValid = true;
            let errorMessage = '';
            
            // Validation selon le type de champ
            switch (fieldName) {
                case 'first_name':
                case 'last_name':
                    if ($field.prop('required') && !value) {
                        isValid = false;
                        errorMessage = `Le ${fieldName === 'first_name' ? 'prénom' : 'nom'} est obligatoire`;
                    } else if (value && value.length < 2) {
                        isValid = false;
                        errorMessage = 'Minimum 2 caractères';
                    } else if (value && !/^[a-zA-ZÀ-ÿ\s-']+$/.test(value)) {
                        isValid = false;
                        errorMessage = 'Caractères non autorisés';
                    }
                    break;
                    
                case 'email':
                    if ($field.prop('required') && !value) {
                        isValid = false;
                        errorMessage = 'Email obligatoire';
                    } else if (value && !isValidEmail(value)) {
                        isValid = false;
                        errorMessage = 'Format email invalide';
                    }
                    break;
                    
                case 'phone':
                    if ($field.prop('required') && !value) {
                        isValid = false;
                        errorMessage = 'Téléphone obligatoire';
                    } else if (value && !isValidPhone(value)) {
                        isValid = false;
                        errorMessage = 'Format téléphone invalide';
                    }
                    break;
                    
                case 'experience':
                    if ($field.prop('required') && !value) {
                        isValid = false;
                        errorMessage = 'Description obligatoire';
                    } else if (value && value.length < 50) {
                        isValid = false;
                        errorMessage = `${value.length}/50 caractères minimum`;
                    } else if (value && value.length > 1000) {
                        isValid = false;
                        errorMessage = `${value.length}/1000 caractères maximum`;
                    }
                    break;
                    
                case 'linkedin_url':
                    // ✅ CORRIGÉ : LinkedIn optionnel
                    if (value && !value.includes('linkedin.com')) {
                        isValid = false;
                        errorMessage = 'URL LinkedIn invalide';
                    }
                    break;
            }
            
            // Application du résultat
            if (isValid && value) {
                $formGroup.addClass('success');
                showSuccessIcon($field);
            } else if (!isValid) {
                $formGroup.addClass('error');
                $errorMsg.text(errorMessage).css('opacity', 1);
                showErrorIcon($field);
            }
            
            return isValid;
        }

        function validateSpecialties() {
            const $checked = $('input[name="specialties[]"]:checked');
            const $container = $('.trpro-checkbox-grid');
            const $errorMsg = $('#trpro-specialties-error');
            
            $container.removeClass('trpro-error-highlight');
            $errorMsg.text('');
            
            if ($checked.length === 0) {
                $container.addClass('trpro-error-highlight');
                $errorMsg.text('Sélectionnez au moins une spécialité');
                return false;
            } else if ($checked.length > 5) {
                $container.addClass('trpro-error-highlight');
                $errorMsg.text('Maximum 5 spécialités recommandées');
                return false;
            }
            
            return true;
        }

        function validateRgpd() {
            const $checkbox = $('#trpro-rgpd-consent');
            const $container = $('.trpro-required-consent');
            const $errorMsg = $('#trpro-rgpd-error');
            
            $container.removeClass('error');
            $errorMsg.text('');
            
            if (!$checkbox.prop('checked')) {
                $container.addClass('error');
                $errorMsg.text('Consentement RGPD obligatoire');
                return false;
            }
            
            return true;
        }

        // ===== UTILITAIRES DE VALIDATION =====
        
        function isValidEmail(email) {
            const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return regex.test(email);
        }

        function isValidPhone(phone) {
            // Format français: 01 23 45 67 89, +33 1 23 45 67 89, etc.
            const cleanPhone = phone.replace(/[\s.-]/g, '');
            const regex = /^(?:(?:\+|00)33|0)[1-9](?:[0-9]{8})$/;
            return regex.test(cleanPhone);
        }

        // ===== AFFICHAGE DES ERREURS MODERNE =====
        
        function displayErrors(errors) {
            clearStepErrors();
            
            if (errors.length === 0) return;
            
            // Créer le conteneur d'erreurs
            const $errorContainer = $(`
                <div class="trpro-step-errors">
                    <div class="trpro-error-header">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Veuillez corriger les erreurs suivantes :</strong>
                    </div>
                    <ul class="trpro-error-list"></ul>
                </div>
            `);
            
            const $errorList = $errorContainer.find('.trpro-error-list');
            
            errors.forEach(error => {
                const $errorItem = $(`
                    <li class="trpro-error-item">
                        <span class="trpro-error-text">
                            <i class="fas fa-times-circle"></i>
                            ${error.message}
                        </span>
                        <button type="button" class="trpro-error-goto" data-selector="${error.selector}">
                            Corriger
                        </button>
                    </li>
                `);
                
                $errorList.append($errorItem);
                
                // Mettre en évidence le champ
                highlightErrorField(error.selector);
            });
            
            // Insérer le conteneur
            const $currentStep = $(`.trpro-form-step[data-step="${currentStep}"]`);
            $currentStep.prepend($errorContainer);
            
            // Animation d'entrée
            $errorContainer.hide().slideDown(300);
            
            // Gérer les clics "Corriger"
            $errorContainer.find('.trpro-error-goto').on('click', function() {
                const selector = $(this).data('selector');
                scrollToField(selector);
                $(selector).focus();
            });
        }

        function highlightErrorField(selector) {
            const $field = $(selector);
            const $formGroup = $field.closest('.trpro-form-group');
            
            $formGroup.addClass('error');
            $field.addClass('trpro-field-error-highlight');
            
            // Pour les spécialités
            if (selector === '.trpro-checkbox-grid') {
                $(selector).addClass('trpro-error-highlight');
            }
            
            // ✅ NOUVEAU : Pour les régions
            if (selector === '.trpro-regions-grid') {
                $(selector).addClass('trpro-error-highlight');
            }
            
            // Pour RGPD
            if (selector === '#trpro-rgpd-consent') {
                $('.trpro-required-consent').addClass('error');
            }
        }

        function clearStepErrors() {
            $('.trpro-step-errors').remove();
            $('.trpro-form-group').removeClass('error success');
            $('.trpro-error-message').text('').css('opacity', 0);
            $('.trpro-field-error-highlight').removeClass('trpro-field-error-highlight');
            $('.trpro-error-highlight').removeClass('trpro-error-highlight');
            $('.trpro-required-consent').removeClass('error');
            $('#trpro-specialties-error, #trpro-rgpd-error, #trpro-regions-error').text('');
        }

        function scrollToFirstError() {
            const $firstError = $('.trpro-step-errors');
            if ($firstError.length > 0) {
                $('html, body').animate({
                    scrollTop: $firstError.offset().top - 120
                }, 500, 'easeOutCubic');
            }
        }

        function scrollToField(selector) {
            const $field = $(selector);
            if ($field.length > 0) {
                $('html, body').animate({
                    scrollTop: $field.offset().top - 150
                }, 400, 'easeOutCubic');
            }
        }

        // ===== ICÔNES DE VALIDATION =====
        
        function showSuccessIcon($field) {
            const $formGroup = $field.closest('.trpro-form-group');
            $formGroup.find('.validation-icon').remove();
            $formGroup.append('<i class="fas fa-check-circle validation-icon success-icon"></i>');
        }

        function showErrorIcon($field) {
            const $formGroup = $field.closest('.trpro-form-group');
            $formGroup.find('.validation-icon').remove();
            $formGroup.append('<i class="fas fa-times-circle validation-icon error-icon"></i>');
        }

        // ===== GESTION DES FICHIERS MODERNE =====
        
        function initFileUpload() {
            // Clic sur zone d'upload
            $(document).on('click', '.trpro-file-upload-area', function(e) {
                e.preventDefault();
                const targetInput = $(this).data('target');
                if (targetInput) {
                    $(`#${targetInput}`).trigger('click');
                }
            });

            // Drag & Drop avec animations
            $('.trpro-file-upload-area')
                .on('dragover', function(e) {
                    e.preventDefault();
                    $(this).addClass('dragover');
                })
                .on('dragleave', function(e) {
                    e.preventDefault();
                    $(this).removeClass('dragover');
                })
                .on('drop', function(e) {
                    e.preventDefault();
                    $(this).removeClass('dragover');
                    
                    const files = e.originalEvent.dataTransfer.files;
                    const targetInput = $(this).data('target');
                    
                    if (files.length > 0 && targetInput) {
                        const inputElement = $(`#${targetInput}`)[0];
                        if (inputElement) {
                            inputElement.files = files;
                            $(inputElement).trigger('change');
                        }
                    }
                });

            // Changement de fichier
            $(document).on('change', 'input[type="file"]', function() {
                const file = this.files[0];
                const fileId = $(this).attr('id');
                const $preview = $(`#${fileId}-preview`);
                
                if (file) {
                    showFilePreview(file, $preview, fileId);
                    validateFileField($(this), file);
                } else {
                    $preview.removeClass('active').empty();
                }
            });

            // Suppression de fichier
            $(document).on('click', '.trpro-file-remove', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const targetId = $(this).data('target');
                const $input = $(`#${targetId}`);
                $input.val('');
                $(`#${targetId}-preview`).removeClass('active').empty();
                
                // Reset de la validation
                $input.closest('.trpro-form-group').removeClass('error success');
            });
        }

        function validateFileField($input, file) {
            const $formGroup = $input.closest('.trpro-form-group');
            const $errorMsg = $input.siblings('.trpro-error-message');
            const fieldName = $input.attr('name');
            
            $formGroup.removeClass('error success');
            $errorMsg.text('');
            
            let isValid = true;
            let errorMessage = '';
            
            if (fieldName === 'cv_file') {
                if (file.size > 5 * 1024 * 1024) {
                    isValid = false;
                    errorMessage = `Fichier trop volumineux (${formatFileSize(file.size)}). Maximum: 5MB`;
                } else {
                    const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
                    if (!allowedTypes.includes(file.type)) {
                        isValid = false;
                        errorMessage = 'Format non supporté. Utilisez PDF, DOC ou DOCX';
                    }
                }
            } else if (fieldName === 'photo_file') {
                if (file.size > 2 * 1024 * 1024) {
                    isValid = false;
                    errorMessage = `Image trop volumineuse (${formatFileSize(file.size)}). Maximum: 2MB`;
                } else {
                    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                    if (!allowedTypes.includes(file.type)) {
                        isValid = false;
                        errorMessage = 'Format non supporté. Utilisez JPG, PNG ou GIF';
                    }
                }
            }
            
            if (isValid) {
                $formGroup.addClass('success');
            } else {
                $formGroup.addClass('error');
                $errorMsg.text(errorMessage);
            }
        }

        function showFilePreview(file, $preview, fileId) {
            let fileIcon = 'fas fa-file';
            if (file.type.includes('pdf')) fileIcon = 'fas fa-file-pdf';
            else if (file.type.includes('image')) fileIcon = 'fas fa-file-image';
            else if (file.type.includes('word')) fileIcon = 'fas fa-file-word';
            
            const fileSize = formatFileSize(file.size);
            
            const previewHtml = `
                <div class="trpro-file-info">
                    <i class="${fileIcon}"></i>
                    <div class="trpro-file-details">
                        <div class="trpro-file-name">${escapeHtml(file.name)}</div>
                        <div class="trpro-file-size">${fileSize}</div>
                    </div>
                    <button type="button" class="trpro-file-remove" data-target="${fileId}">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            
            $preview.html(previewHtml).addClass('active');
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        // ===== GESTION DES CHECKBOXES MODERNE =====
        
        function initCheckboxes() {
            // Checkboxes spécialités
            $(document).on('click', '.trpro-checkbox-item', function(e) {
                if ($(e.target).is('input[type="checkbox"]') || $(e.target).is('label')) {
                    return;
                }
                
                const $checkbox = $(this).find('input[type="checkbox"]');
                $checkbox.prop('checked', !$checkbox.prop('checked')).trigger('change');
            });
            
            // Checkboxes consentement
            $(document).on('click', '.trpro-consent-wrapper', function(e) {
                if ($(e.target).is('input[type="checkbox"]') || $(e.target).is('label')) {
                    return;
                }
                
                const $checkbox = $(this).find('input[type="checkbox"]');
                $checkbox.prop('checked', !$checkbox.prop('checked')).trigger('change');
            });
        }

        // ===== GÉNÉRATION DU RÉSUMÉ AMÉLIORÉ =====
        
        function generateSummary() {
            const $summary = $('#trpro-registration-summary');
            $summary.empty();
            
            console.log('📋 Génération du résumé...');
            
            // Informations personnelles
            addSummaryItem($summary, 'Nom complet', `${$('#trpro-first-name').val()} ${$('#trpro-last-name').val()}`);
            addSummaryItem($summary, 'Email', $('#trpro-email').val());
            addSummaryItem($summary, 'Téléphone', $('#trpro-phone').val());
            
            const company = $('#trpro-company').val();
            if (company) {
                addSummaryItem($summary, 'Entreprise', company);
            }
            
            // ✅ NOUVEAU : LinkedIn si renseigné
            const linkedin = $('#trpro-linkedin-url').val();
            if (linkedin) {
                addSummaryItem($summary, 'LinkedIn', linkedin);
            }
            
            // Spécialités
            const specialties = [];
            $('input[name="specialties[]"]:checked').each(function() {
                const label = $(this).siblings('label').text().trim();
                specialties.push(label);
            });
            if (specialties.length > 0) {
                addSummaryItem($summary, 'Spécialités', specialties.join(', '));
            }
            
            // ✅ NOUVEAU : Zones d'intervention
            const regions = [];
            $('input[name="intervention_regions[]"]:checked').each(function() {
                const label = $(this).siblings('label').text().trim();
                regions.push(label);
            });
            if (regions.length > 0) {
                addSummaryItem($summary, 'Zones d\'intervention', regions.join(', '));
            }
            
            // Disponibilité
            const availability = $('#trpro-availability').val();
            if (availability) {
                const availabilityText = $('#trpro-availability option:selected').text();
                addSummaryItem($summary, 'Disponibilité', availabilityText);
            }
            
            // Tarif horaire
            const hourlyRate = $('#trpro-hourly-rate').val();
            if (hourlyRate) {
                addSummaryItem($summary, 'Tarif horaire', hourlyRate);
            }
            
            // Fichiers
            const cvFile = $('#trpro-cv-file')[0].files[0];
            if (cvFile) {
                addSummaryItem($summary, 'CV', `${cvFile.name} (${formatFileSize(cvFile.size)})`);
            }
            
            const photoFile = $('#trpro-photo-file')[0].files[0];
            if (photoFile) {
                addSummaryItem($summary, 'Photo', `${photoFile.name} (${formatFileSize(photoFile.size)})`);
            }
        }

        function addSummaryItem($container, label, value) {
            if (!value) return;
            
            const $item = $(`
                <div class="trpro-summary-item">
                    <div class="trpro-summary-label">${escapeHtml(label)}</div>
                    <div class="trpro-summary-value">${escapeHtml(value)}</div>
                </div>
            `);
            
            $container.append($item);
        }

        // ===== SOUMISSION DU FORMULAIRE =====
        
        function submitForm() {
            console.log('📤 Soumission du formulaire...');
            formSubmitting = true;
            
            // Afficher le loading
            elements.loading.fadeIn(200);
            elements.submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Envoi en cours...');
            
            // Préparer les données
            const formData = new FormData(elements.form[0]);
            formData.append('action', 'submit_trainer_registration');
            formData.append('nonce', trainer_ajax.nonce);
            
            $.ajax({
                url: trainer_ajax.ajax_url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                timeout: 30000,
                success: function(response) {
                    console.log('✅ Réponse serveur:', response);
                    handleFormResponse(response);
                },
                error: function(xhr, status, error) {
                    console.error('❌ Erreur AJAX:', {xhr, status, error});
                    handleFormError(xhr, status, error);
                },
                complete: function() {
                    elements.loading.fadeOut(200);
                    elements.submitBtn.prop('disabled', false).html('<i class="fas fa-paper-plane"></i> Envoyer ma candidature');
                    formSubmitting = false;
                }
            });
        }

        function handleFormResponse(response) {
            if (response.success) {
                showMessage('success', response.data.message || 'Inscription réussie !');
                
                // Reset du formulaire
                elements.form[0].reset();
                $('.trpro-file-preview').removeClass('active').empty();
                $('.trpro-regions-counter').hide(); // ✅ NOUVEAU
                currentStep = 1;
                showStep(currentStep);
                
                scrollToMessage();
                
                if (response.data.redirect) {
                    setTimeout(() => {
                        window.location.href = response.data.redirect;
                    }, 3000);
                }
            } else {
                const errorMessage = response.data?.message || 'Erreur lors de l\'inscription';
                showMessage('error', errorMessage);
            }
        }

        function handleFormError(xhr, status, error) {
            let errorMessage = 'Erreur de connexion. Veuillez réessayer.';
            
            if (status === 'timeout') {
                errorMessage = 'La requête a expiré. Veuillez réessayer.';
            } else if (xhr.responseJSON?.data?.message) {
                errorMessage = xhr.responseJSON.data.message;
            }
            
            showMessage('error', errorMessage);
        }

        function showMessage(type, message) {
            const icon = type === 'success' ? 'check-circle' : 'exclamation-circle';
            
            elements.messages
                .removeClass('success error')
                .addClass(type)
                .html(`<i class="fas fa-${icon}"></i> ${escapeHtml(message)}`)
                .fadeIn(300);
            
            if (type === 'success') {
                setTimeout(() => {
                    elements.messages.fadeOut(300);
                }, 5000);
            }
        }

        function scrollToMessage() {
            if (elements.messages.length > 0) {
                $('html, body').animate({
                    scrollTop: elements.messages.offset().top - 100
                }, 400);
            }
        }

        // ===== RECHERCHE DE FORMATEURS =====
        
        function initSearch() {
            const $searchInput = $('#trpro-trainer-search-input');
            const $searchBtn = $('#trpro-search-trainers-btn');
            const $specialtyFilter = $('#trpro-specialty-filter');
            const $searchResults = $('#trpro-search-results');
            const $searchLoading = $('#trpro-search-loading');
            
            // Événements de recherche
            $searchBtn.on('click', performSearch);
            $specialtyFilter.on('change', performSearch);
            
            // Recherche en temps réel avec debounce
            $searchInput.on('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    if ($searchInput.val().length >= 3 || $searchInput.val().length === 0) {
                        performSearch();
                    }
                }, 500);
            });
            
            // Recherche sur Enter
            $searchInput.on('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    performSearch();
                }
            });
            
            // Tags de suggestion
            $('.trpro-suggestion-tag').on('click', function(e) {
                e.preventDefault();
                const searchTerm = $(this).data('search');
                const category = $(this).data('category');
                
                $searchInput.val(searchTerm);
                if (category && category !== 'all') {
                    $specialtyFilter.val(category);
                }
                
                // Effet visuel
                $(this).css('transform', 'scale(0.95)');
                setTimeout(() => {
                    $(this).css('transform', '');
                    performSearch();
                }, 150);
            });
            
            function performSearch() {
                const query = $searchInput.val().trim();
                const specialty = $specialtyFilter.val();
                
                if (!query && specialty === 'all') {
                    showSearchPlaceholder();
                    return;
                }
                
                console.log('🔍 Recherche:', {query, specialty});
                
                // Afficher loading
                $searchResults.hide();
                $searchLoading.show();
                
                // Requête AJAX
                $.ajax({
                    url: trainer_ajax.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'search_trainers',
                        nonce: trainer_ajax.nonce,
                        search_term: query,
                        specialty_filter: specialty
                    },
                    success: function(response) {
                        console.log('✅ Résultats recherche:', response);
                        handleSearchResults(response);
                    },
                    error: function(xhr, status, error) {
                        console.error('❌ Erreur recherche:', error);
                        showSearchError();
                    },
                    complete: function() {
                        $searchLoading.hide();
                        $searchResults.show();
                    }
                });
            }
            
            function handleSearchResults(response) {
                if (response.success && response.data.html) {
                    $searchResults.html(response.data.html);
                    
                    // Animation d'entrée
                    $searchResults.css({opacity: 0, transform: 'translateY(20px)'});
                    $searchResults.animate({opacity: 1}, 400);
                    $searchResults.css('transform', 'translateY(0)');
                    
                } else {
                    showNoResults();
                }
            }
            
            function showSearchPlaceholder() {
                $searchResults.html(`
                    <div class="trpro-search-placeholder">
                        <div class="trpro-placeholder-content">
                            <div class="trpro-placeholder-icon">
                                <i class="fas fa-search"></i>
                            </div>
                            <h4>Commencez votre recherche</h4>
                            <p>Utilisez la barre de recherche pour trouver des formateurs experts</p>
                        </div>
                    </div>
                `).show();
            }
            
            function showNoResults() {
                $searchResults.html(`
                    <div class="trpro-no-results">
                        <div class="trpro-empty-icon">
                            <i class="fas fa-search-minus"></i>
                        </div>
                        <h3>Aucun résultat trouvé</h3>
                        <p>Essayez de modifier vos critères de recherche</p>
                    </div>
                `).show();
            }
            
            function showSearchError() {
                $searchResults.html(`
                    <div class="trpro-search-error">
                        <div class="trpro-error-icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <h3>Erreur de recherche</h3>
                        <p>Impossible d'effectuer la recherche. Veuillez réessayer.</p>
                    </div>
                `).show();
            }
        }

        // ===== CARTES DE FORMATEURS =====
        
        function initTrainerCards() {
            // Bouton détails
            $(document).on('click', '.trpro-btn-info, .trpro-btn-details', function(e) {
                e.preventDefault();
                const trainerId = $(this).data('trainer-id');
                showTrainerModal(trainerId);
            });
            
            // Fermeture modal
            $(document).on('click', '.trpro-modal-close, .trpro-modal-backdrop', function() {
                const trainerId = $(this).data('trainer-id') || $(this).closest('.trpro-trainer-modal').attr('id')?.replace('trpro-modal-', '');
                if (trainerId) {
                    hideTrainerModal(trainerId);
                }
            });
            
            // Fermeture par Escape
            $(document).on('keydown', function(e) {
                if (e.key === 'Escape') {
                    $('.trpro-trainer-modal:visible, .trpro-modal-overlay:visible').each(function() {
                        const trainerId = $(this).attr('id')?.replace('trpro-modal-', '');
                        if (trainerId) {
                            hideTrainerModal(trainerId);
                        }
                    });
                }
            });
            
            function showTrainerModal(trainerId) {
                const $modal = $(`#trpro-modal-${trainerId}`);
                if ($modal.length > 0) {
                    $modal.fadeIn(300).addClass('active');
                    $('body').addClass('modal-open');
                }
            }
            
            function hideTrainerModal(trainerId) {
                const $modal = $(`#trpro-modal-${trainerId}`);
                if ($modal.length > 0) {
                    $modal.fadeOut(300).removeClass('active');
                    $('body').removeClass('modal-open');
                }
            }
        }

        // ===== NOUVEAU : GESTION DES PROFILS DÉTAILLÉS =====
        
        function initProfileModals() {
            // Boutons pour afficher le profil détaillé
            $(document).on('click', '.trpro-btn-profile', function(e) {
                e.preventDefault();
                const trainerId = $(this).data('trainer-id');
                loadTrainerProfile(trainerId);
            });
        }

        function loadTrainerProfile(trainerId) {
            // Afficher un modal de chargement
            showProfileLoadingModal();
            
            // Requête AJAX pour récupérer le profil détaillé
            $.ajax({
                url: trainer_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'get_trainer_profile',
                    nonce: trainer_ajax.nonce,
                    trainer_id: trainerId
                },
                success: function(response) {
                    hideProfileLoadingModal();
                    
                    if (response.success && response.data) {
                        showProfileModal(response.data);
                    } else {
                        showProfileError(response.data?.message || 'Erreur lors du chargement du profil');
                    }
                },
                error: function() {
                    hideProfileLoadingModal();
                    showProfileError('Erreur de connexion');
                }
            });
        }

        function showProfileLoadingModal() {
            const loadingHTML = `
                <div class="trpro-modal-overlay active" id="trpro-profile-loading-modal">
                    <div class="trpro-modal-container">
                        <div class="trpro-modal-content">
                            <div class="trpro-modal-loading">
                                <div class="trpro-spinner"></div>
                                <p>Chargement du profil...</p>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            $('body').append(loadingHTML);
            $('body').addClass('modal-open');
        }

        function hideProfileLoadingModal() {
            $('#trpro-profile-loading-modal').remove();
            $('body').removeClass('modal-open');
        }

        function showProfileModal(profileData) {
            const regions = profileData.intervention_regions || [];
            const specialties = profileData.specialties || [];
            
            const modalHTML = `
                <div class="trpro-modal-overlay active" id="trpro-profile-modal-${profileData.id}">
                    <div class="trpro-modal-container">
                        <div class="trpro-modal-header">
                            <div class="trpro-modal-title">
                                <div class="trpro-modal-avatar">
                                    ${profileData.photo_url ? 
                                        `<img src="${profileData.photo_url}" alt="Photo du formateur">` :
                                        `<div class="trpro-modal-avatar-placeholder"><i class="fas fa-user-graduate"></i></div>`
                                    }
                                </div>
                                <div class="trpro-modal-info">
                                    <h4>${escapeHtml(profileData.display_name)}</h4>
                                    <p>Formateur Expert #${String(profileData.id).padStart(4, '0')}</p>
                                    ${profileData.company ? `<p class="trpro-modal-company">${escapeHtml(profileData.company)}</p>` : ''}
                                </div>
                            </div>
                            <button class="trpro-modal-close"><i class="fas fa-times"></i></button>
                        </div>
                        
                        <div class="trpro-modal-content">
                            ${regions.length > 0 ? `
                                <div class="trpro-modal-section">
                                    <h5><i class="fas fa-map-marker-alt"></i> Zones d'intervention</h5>
                                    <div class="trpro-modal-zones">
                                        ${regions.map(region => `
                                            <span class="trpro-zone-chip">
                                                <i class="fas fa-map-pin"></i>
                                                ${escapeHtml(region)}
                                            </span>
                                        `).join('')}
                                    </div>
                                </div>
                            ` : ''}
                            
                            <div class="trpro-modal-section">
                                <h5><i class="fas fa-cogs"></i> Compétences techniques</h5>
                                <div class="trpro-detailed-specialties">
                                    ${specialties.map(specialty => `
                                        <div class="trpro-specialty-chip">
                                            <i class="fas fa-cog"></i>
                                            <span>${escapeHtml(specialty)}</span>
                                        </div>
                                    `).join('')}
                                </div>
                            </div>
                            
                            ${profileData.availability || profileData.hourly_rate ? `
                                <div class="trpro-modal-section">
                                    <h5><i class="fas fa-clock"></i> Disponibilité & Tarifs</h5>
                                    <div class="trpro-availability-info">
                                        ${profileData.availability ? `
                                            <div class="trpro-info-item">
                                                <strong>Disponibilité :</strong>
                                                <span>${escapeHtml(profileData.availability)}</span>
                                            </div>
                                        ` : ''}
                                        ${profileData.hourly_rate ? `
                                            <div class="trpro-info-item">
                                                <strong>Tarif horaire :</strong>
                                                <span>${escapeHtml(profileData.hourly_rate)}</span>
                                            </div>
                                        ` : ''}
                                    </div>
                                </div>
                            ` : ''}
                            
                            ${profileData.experience ? `
                                <div class="trpro-modal-section">
                                    <h5><i class="fas fa-briefcase"></i> Expérience professionnelle</h5>
                                    <div class="trpro-experience-full">
                                        ${escapeHtml(profileData.experience).replace(/\n/g, '<br>')}
                                    </div>
                                </div>
                            ` : ''}
                            
                            ${profileData.bio ? `
                                <div class="trpro-modal-section">
                                    <h5><i class="fas fa-user"></i> Présentation</h5>
                                    <div class="trpro-bio-full">
                                        ${escapeHtml(profileData.bio).replace(/\n/g, '<br>')}
                                    </div>
                                </div>
                            ` : ''}
                            
                            <div class="trpro-modal-actions">
                                <a href="mailto:${trainer_ajax.contact_email}?subject=Contact formateur %23${String(profileData.id).padStart(4, '0')}" 
                                   class="trpro-btn trpro-btn-primary trpro-btn-large">
                                    <i class="fas fa-envelope"></i>
                                    Contacter par Email
                                </a>
                                
                                ${profileData.linkedin_url ? `
                                    <a href="${profileData.linkedin_url}" 
                                       target="_blank"
                                       rel="noopener noreferrer"
                                       class="trpro-btn trpro-btn-outline trpro-btn-large">
                                        <i class="fab fa-linkedin"></i>
                                        Voir LinkedIn
                                    </a>
                                ` : ''}
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            $('body').append(modalHTML);
            $('body').addClass('modal-open');
            
            // Gestionnaire de fermeture
            $(`#trpro-profile-modal-${profileData.id} .trpro-modal-close`).on('click', function() {
                $(`#trpro-profile-modal-${profileData.id}`).fadeOut(300, function() {
                    $(this).remove();
                    $('body').removeClass('modal-open');
                });
            });
        }

        function showProfileError(message) {
            const errorHTML = `
                <div class="trpro-modal-overlay active" id="trpro-profile-error-modal">
                    <div class="trpro-modal-container">
                        <div class="trpro-modal-header">
                            <h4>Erreur</h4>
                            <button class="trpro-modal-close"><i class="fas fa-times"></i></button>
                        </div>
                        <div class="trpro-modal-content">
                            <div class="trpro-error-state">
                                <div class="trpro-error-icon">
                                    <i class="fas fa-exclamation-triangle"></i>
                                </div>
                                <p>${escapeHtml(message)}</p>
                                <button class="trpro-btn trpro-btn-primary" onclick="$('#trpro-profile-error-modal').remove(); $('body').removeClass('modal-open');">
                                    Fermer
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            $('body').append(errorHTML);
            $('body').addClass('modal-open');
            
            $('#trpro-profile-error-modal .trpro-modal-close').on('click', function() {
                $('#trpro-profile-error-modal').remove();
                $('body').removeClass('modal-open');
            });
        }

        // ===== ANIMATIONS MODERNES =====
        
        function initFormAnimations() {
            // Animation d'easing personnalisée
            $.easing.easeOutCubic = function(x, t, b, c, d) {
                return c*((t=t/d-1)*t*t + 1) + b;
            };
            
            // Animation des champs au focus
            elements.form.find('input, textarea, select').on('focus', function() {
                $(this).closest('.trpro-form-group').addClass('focused');
            }).on('blur', function() {
                $(this).closest('.trpro-form-group').removeClass('focused');
            });
        }

        // ===== ANIMATIONS GÉNÉRALES =====
        
        function initGlobalAnimations() {
            // Intersection Observer pour les animations
            if (typeof IntersectionObserver !== 'undefined') {
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('trpro-fade-in');
                        }
                    });
                }, { threshold: 0.1 });

                // Observer les éléments animables
                $('.trpro-specialty-card, .trpro-card, .trpro-trainer-card').each(function() {
                    observer.observe(this);
                });
            }
            
            // Parallax simple pour le hero
            $(window).on('scroll', function() {
                const scrollTop = $(this).scrollTop();
                const $heroBackground = $('.trpro-hero-background');
                
                if ($heroBackground.length > 0 && scrollTop < $(window).height()) {
                    $heroBackground.css('transform', `translateY(${scrollTop * 0.5}px)`);
                }
            });
        }

        // ===== UTILITAIRES =====
        
        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
        }

        // ===== RESPONSIVE HELPERS =====
        
        function isMobile() {
            return window.innerWidth <= 768;
        }

        function isTablet() {
            return window.innerWidth <= 1024 && window.innerWidth > 768;
        }

        // ===== PERFORMANCE OPTIMIZATIONS =====
        
        // Debounce function
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        // Throttle function
        function throttle(func, limit) {
            let inThrottle;
            return function() {
                const args = arguments;
                const context = this;
                if (!inThrottle) {
                    func.apply(context, args);
                    inThrottle = true;
                    setTimeout(() => inThrottle = false, limit);
                }
            };
        }

        // ===== EVENT LISTENERS GLOBAUX =====
        
        // Gestion des clics outside pour fermer les modales
        $(document).on('click', '.trpro-modal-overlay', function(e) {
            if (e.target === this) {
                $(this).fadeOut(300, function() {
                    $(this).remove();
                });
                $('body').removeClass('modal-open');
            }
        });

        // Gestion responsive
        $(window).on('resize', debounce(function() {
            // Ajustements responsive si nécessaire
            if (isMobile() && $('.trpro-registration-form').is(':visible')) {
                console.log('📱 Mode mobile détecté');
            }
        }, 250));

        // ===== STYLES CSS POUR LES NOUVEAUX ÉLÉMENTS =====
        
        // Ajouter les styles CSS pour le compteur de régions
        $('<style>').text(`
            .trpro-regions-counter {
                margin-top: 12px;
                text-align: center;
                padding: 8px 16px;
                background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
                color: white;
                border-radius: 20px;
                font-size: 14px;
                font-weight: 600;
                display: none;
                animation: slideDown 0.3s ease-out;
            }
            
            .trpro-counter-text {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
            }
            
            .trpro-zone-chip {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                background: #f0f9ff;
                color: #0369a1;
                padding: 8px 12px;
                border-radius: 20px;
                border: 1px solid #0ea5e9;
                font-size: 0.875rem;
                font-weight: 500;
                margin: 4px;
            }
            
            .trpro-zone-chip i {
                font-size: 0.8rem;
            }
        `).appendTo('head');

        // ===== DEBUG HELPER (DÉVELOPPEMENT) =====
        
        if (window.location.hostname === 'localhost' || window.location.hostname.includes('dev')) {
            window.trainerDebug = {
                // Formulaire
                currentStep: () => currentStep,
                validateStep: () => validateCurrentStep(),
                formData: () => new FormData(elements.form[0]),
                resetForm: () => {
                    elements.form[0].reset();
                    $('.trpro-regions-counter').hide();
                    currentStep = 1;
                    showStep(1);
                },
                showStep: (step) => showStep(step),
                
                // Recherche
                performSearch: () => {
                    if (typeof performSearch === 'function') {
                        performSearch();
                    }
                },
                
                // Profils
                showProfile: (trainerId) => loadTrainerProfile(trainerId),
                
                // Utilitaires
                isMobile: () => isMobile(),
                isTablet: () => isTablet(),
                
                // État global
                getState: () => ({
                    currentStep,
                    formSubmitting,
                    searchTimeout,
                    validationTimeout
                })
            };
            console.log('🛠️ Debug helper disponible: window.trainerDebug');
        }

        // ===== INITIALISATION FINALE =====
        
        console.log('✅ Trainer Registration Pro: Initialisation complète terminée');
        console.log('📊 Composants initialisés:', {
            formulaire: elements.form.length > 0,
            recherche: $('#trpro-trainer-search').length > 0,
            cartes: $('.trpro-trainer-card').length,
            profils: true,
            regions: true,
            animations: true
        });
        
        // Notification de succès d'initialisation
        if (elements.form.length > 0) {
            console.log('🎯 Formulaire prêt - Navigation multi-étapes activée avec régions');
        }
        
        // Vérification de la compatibilité
        const features = {
            intersectionObserver: typeof IntersectionObserver !== 'undefined',
            localStorage: typeof Storage !== 'undefined',
            formData: typeof FormData !== 'undefined',
            fetch: typeof fetch !== 'undefined'
        };
        
        console.log('🔧 Support navigateur:', features);
        
        if (!features.formData) {
            console.warn('⚠️ FormData non supporté - Upload de fichiers limité');
        }
        
        if (!features.intersectionObserver) {
            console.warn('⚠️ IntersectionObserver non supporté - Animations limitées');
        }

    });

})(jQuery);
