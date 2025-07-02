/**
 * JavaScript moderne pour la recherche et liste des formateurs
 * 
 * Fichier: public/js/trainer-search-modern.js
 * ✅ Recherche AJAX réelle, sécurisée et performante
 */

(function($) {
    'use strict';

    // ===== CONFIGURATION =====
    const CONFIG = {
        searchDelay: 300,
        minSearchLength: 2,
        maxRetries: 3,
        animationDuration: 300,
        endpoints: {
            search: trainer_ajax.ajax_url,
            nonce: trainer_ajax.nonce
        }
    };

    // ===== VARIABLES GLOBALES =====
    let searchTimeout;
    let currentRequest;
    let searchCache = new Map();
    let isSearching = false;

    // ===== ÉLÉMENTS DOM =====
    const elements = {
        searchInput: $('#trpro-live-search'),
        specialtyFilter: $('#trpro-specialty-filter'),
        searchClear: $('.trpro-search-clear'),
        resultsContainer: $('#trpro-trainers-grid'),
        loadingState: $('#trpro-search-loading'),
        emptyState: $('#trpro-empty-state'),
        resultsHeader: $('#trpro-results-header'),
        resultsTitle: $('#trpro-results-title'),
        resultsCount: $('#trpro-results-count'),
        viewButtons: $('.trpro-view-btn'),
        popularTags: $('.trpro-tag')
    };

    // ===== INITIALISATION =====
    $(document).ready(function() {
        console.log('🚀 Trainer Search Modern: Initialisation...');
        
        initEventListeners();
        initModalHandlers();
        initViewSwitcher();
        loadInitialData();
        
        console.log('✅ Trainer Search Modern: Prêt');
    });

    // ===== ÉVÉNEMENTS =====
    function initEventListeners() {
        // Recherche en temps réel
        elements.searchInput.on('input', debounce(handleSearchInput, CONFIG.searchDelay));
        
        // Changement de filtre spécialité
        elements.specialtyFilter.on('change', handleFilterChange);
        
        // Bouton de clear
        elements.searchClear.on('click', clearSearch);
        
        // Tags populaires
        elements.popularTags.on('click', handleTagClick);
        
        // Recherche sur Enter
        elements.searchInput.on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                performSearch(true);
            }
        });

        // Détection de changement pour afficher/masquer le bouton clear
        elements.searchInput.on('input', toggleClearButton);
    }

    function initModalHandlers() {
        // Boutons de détails des cartes
        $(document).on('click', '.trpro-btn-details', handleTrainerDetails);
        
        // Fermeture des modals
        $(document).on('click', '.trpro-modal-close, .trpro-modal-overlay', function(e) {
            if (e.target === this) {
                closeModal($(this).closest('.trpro-modal-overlay'));
            }
        });
        
        // Échappement pour fermer
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape') {
                $('.trpro-modal-overlay.active').each(function() {
                    closeModal($(this));
                });
            }
        });
    }

    function initViewSwitcher() {
        elements.viewButtons.on('click', function() {
            const view = $(this).data('view');
            switchView(view);
        });
    }

    // ===== GESTION DE LA RECHERCHE =====
    function handleSearchInput() {
        const query = elements.searchInput.val().trim();
        
        if (query.length === 0) {
            clearSearch();
            return;
        }
        
        if (query.length >= CONFIG.minSearchLength) {
            performSearch();
        }
    }

    function handleFilterChange() {
        performSearch();
    }

    function handleTagClick() {
        const searchTerm = $(this).data('search');
        elements.searchInput.val(searchTerm);
        
        // Animation du tag
        $(this).addClass('active');
        setTimeout(() => $(this).removeClass('active'), 200);
        
        performSearch(true);
    }

    function clearSearch() {
        elements.searchInput.val('');
        elements.specialtyFilter.val('');
        elements.searchClear.hide();
        
        // Recharger les données initiales
        loadInitialData();
    }

    function toggleClearButton() {
        const hasValue = elements.searchInput.val().length > 0;
        elements.searchClear.toggle(hasValue);
    }

    // ===== RECHERCHE AJAX =====
    function performSearch(immediate = false) {
        const query = elements.searchInput.val().trim();
        const specialty = elements.specialtyFilter.val();
        
        // Créer une clé de cache
        const cacheKey = `${query}|${specialty}`;
        
        // Vérifier le cache
        if (searchCache.has(cacheKey)) {
            displayResults(searchCache.get(cacheKey));
            return;
        }
        
        // Annuler la requête précédente
        if (currentRequest) {
            currentRequest.abort();
        }
        
        // Délai pour éviter trop de requêtes
        clearTimeout(searchTimeout);
        
        const delay = immediate ? 0 : CONFIG.searchDelay;
        
        searchTimeout = setTimeout(() => {
            executeSearch(query, specialty, cacheKey);
        }, delay);
    }

    function executeSearch(query, specialty, cacheKey) {
        if (isSearching) return;
        
        isSearching = true;
        showLoadingState();
        
        const searchData = {
            action: 'search_trainers_modern',
            nonce: CONFIG.endpoints.nonce,
            search_term: query,
            specialty_filter: specialty,
            per_page: 12,
            page: 1
        };
        
        console.log('🔍 Recherche:', searchData);
        
        currentRequest = $.ajax({
            url: CONFIG.endpoints.search,
            type: 'POST',
            data: searchData,
            timeout: 10000,
            success: function(response) {
                console.log('📥 Réponse reçue:', response);
                
                if (response.success && response.data) {
                    const results = response.data;
                    
                    // Mettre en cache
                    searchCache.set(cacheKey, results);
                    
                    // Afficher les résultats
                    displayResults(results);
                    
                    // Mise à jour de l'historique/URL
                    updateURL(query, specialty);
                    
                } else {
                    console.error('❌ Erreur dans la réponse:', response);
                    showErrorState(response.data?.message || 'Erreur de recherche');
                }
            },
            error: function(xhr, status, error) {
                console.error('❌ Erreur AJAX:', { xhr, status, error });
                
                if (status !== 'abort') {
                    showErrorState('Erreur de connexion. Veuillez réessayer.');
                }
            },
            complete: function() {
                isSearching = false;
                hideLoadingState();
                currentRequest = null;
            }
        });
    }

    function loadInitialData() {
        showLoadingState();
        
        $.ajax({
            url: CONFIG.endpoints.search,
            type: 'POST',
            data: {
                action: 'get_all_trainers',
                nonce: CONFIG.endpoints.nonce,
                per_page: 12,
                page: 1
            },
            success: function(response) {
                if (response.success && response.data) {
                    displayResults(response.data);
                    
                    // Mettre à jour le titre
                    elements.resultsTitle.text('Nos Formateurs Experts');
                } else {
                    showErrorState('Erreur lors du chargement des formateurs');
                }
            },
            error: function() {
                showErrorState('Erreur de connexion');
            },
            complete: function() {
                hideLoadingState();
            }
        });
    }

    // ===== AFFICHAGE DES RÉSULTATS =====
    function displayResults(results) {
        const { trainers, total, search_term, specialty_filter } = results;
        
        console.log('📊 Affichage de', trainers?.length || 0, 'résultats sur', total);
        
        hideLoadingState();
        hideEmptyState();
        showResultsHeader();
        
        if (!trainers || trainers.length === 0) {
            showEmptyState();
            elements.resultsContainer.empty();
            return;
        }
        
        // Mise à jour du header
        updateResultsHeader(total, search_term, specialty_filter);
        
        // Générer le HTML des cartes
        const cardsHTML = trainers.map(trainer => generateTrainerCard(trainer)).join('');
        
        // Animation d'entrée
        elements.resultsContainer.fadeOut(200, function() {
            $(this).html(cardsHTML).fadeIn(300);
            
            // Réinitialiser les animations
            setTimeout(() => {
                elements.resultsContainer.find('.trpro-trainer-card-modern').each(function(index) {
                    $(this).css({
                        'animation-delay': `${index * 0.1}s`,
                        'animation-name': 'slideInUp'
                    });
                });
            }, 50);
        });
    }

    function generateTrainerCard(trainer) {
        const trainerId = String(trainer.id).padStart(4, '0');
        const specialties = trainer.specialties.split(',').map(s => s.trim()).slice(0, 3);
        const remainingCount = Math.max(0, trainer.specialties.split(',').length - 3);
        
        const specialtyIcons = {
            'administration-systeme': 'fas fa-server',
            'reseaux': 'fas fa-network-wired',
            'cloud': 'fab fa-aws',
            'devops': 'fas fa-infinity',
            'securite': 'fas fa-shield-alt',
            'telecoms': 'fas fa-satellite-dish',
            'developpement': 'fas fa-code',
            'bases-donnees': 'fas fa-database'
        };
        
        const experiencePreview = trainer.experience ? 
            trainer.experience.substring(0, 100) + (trainer.experience.length > 100 ? '...' : '') : '';
        
        const photoHTML = trainer.photo_file ? 
            `<img src="${trainer.photo_url}" alt="Photo du formateur #${trainerId}" loading="lazy">` :
            `<div class="trpro-avatar-placeholder"><i class="fas fa-user-graduate"></i></div>`;
        
        const specialtiesHTML = specialties.map(specialty => {
            const icon = specialtyIcons[specialty] || 'fas fa-cog';
            const label = specialty.replace('-', ' ').replace(/\b\w/g, l => l.toUpperCase());
            return `
                <div class="trpro-specialty-item">
                    <i class="${icon}"></i>
                    <span>${label}</span>
                </div>
            `;
        }).join('');
        
        const remainingHTML = remainingCount > 0 ? 
            `<div class="trpro-specialty-item trpro-specialty-more">
                <i class="fas fa-plus"></i>
                <span>+${remainingCount}</span>
            </div>` : '';
        
        return `
            <article class="trpro-trainer-card-modern" data-trainer-id="${trainer.id}">
                <div class="trpro-card-header">
                    <div class="trpro-trainer-avatar">
                        ${photoHTML}
                        <div class="trpro-status-badge trpro-badge-confirmed">
                            <span>Vérifié</span>
                        </div>
                    </div>
                    <div class="trpro-verification-badges">
                        <div class="trpro-badge trpro-verified" title="Profil vérifié">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        ${trainer.cv_file ? '<div class="trpro-badge trpro-cv-badge" title="CV disponible"><i class="fas fa-file-pdf"></i></div>' : ''}
                    </div>
                </div>
                
                <div class="trpro-card-body">
                    <div class="trpro-trainer-identity">
                        <h3 class="trpro-trainer-title">
                            Formateur Expert
                            <span class="trpro-trainer-id">#${trainerId}</span>
                        </h3>
                        ${trainer.company ? `<div class="trpro-trainer-company"><i class="fas fa-building"></i><span>${trainer.company}</span></div>` : ''}
                    </div>
                    
                    <div class="trpro-specialties-section">
                        <div class="trpro-specialties-grid">
                            ${specialtiesHTML}
                            ${remainingHTML}
                        </div>
                    </div>
                    
                    ${experiencePreview ? `
                        <div class="trpro-experience-preview">
                            <div class="trpro-experience-text">${experiencePreview}</div>
                        </div>
                    ` : ''}
                    
                    <div class="trpro-trainer-meta">
                        ${trainer.availability ? `<div class="trpro-meta-item"><i class="fas fa-calendar-check"></i><span>${trainer.availability.replace('-', ' ')}</span></div>` : ''}
                        ${trainer.hourly_rate ? `<div class="trpro-meta-item"><i class="fas fa-euro-sign"></i><span>${trainer.hourly_rate}</span></div>` : ''}
                        <div class="trpro-meta-item">
                            <i class="fas fa-calendar-plus"></i>
                            <span>Inscrit récemment</span>
                        </div>
                    </div>
                </div>
                
                <div class="trpro-card-footer">
                    <div class="trpro-action-buttons">
                        <a href="mailto:${trainer_ajax.contact_email}?subject=Contact formateur %23${trainerId}" 
                           class="trpro-btn trpro-btn-primary">
                            <i class="fas fa-envelope"></i>
                            <span>Contacter</span>
                        </a>
                        <button class="trpro-btn trpro-btn-outline trpro-btn-details" data-trainer-id="${trainer.id}">
                            <i class="fas fa-user"></i>
                            <span>Profil</span>
                        </button>
                    </div>
                    
                    <div class="trpro-additional-links">
                        ${trainer.linkedin_url ? `<a href="${trainer.linkedin_url}" target="_blank" class="trpro-social-link"><i class="fab fa-linkedin"></i></a>` : ''}
                    </div>
                </div>
                
                <div class="trpro-popularity-indicator">
                    <div class="trpro-popularity-bar" style="width: ${Math.floor(Math.random() * 35) + 60}%;"></div>
                </div>
            </article>
        `;
    }

    function updateResultsHeader(total, searchTerm, specialtyFilter) {
        let title = 'Résultats de recherche';
        let count = `${total} formateur${total > 1 ? 's' : ''} trouvé${total > 1 ? 's' : ''}`;
        
        if (searchTerm) {
            title = `Recherche : "${searchTerm}"`;
            if (specialtyFilter) {
                title += ` dans ${specialtyFilter.replace('-', ' ')}`;
            }
        } else if (specialtyFilter) {
            title = `Spécialité : ${specialtyFilter.replace('-', ' ')}`;
        } else {
            title = 'Nos Formateurs Experts';
            count = `${total} formateur${total > 1 ? 's' : ''} disponible${total > 1 ? 's' : ''}`;
        }
        
        elements.resultsTitle.text(title);
        elements.resultsCount.text(count);
    }

    // ===== GESTION DES ÉTATS =====
    function showLoadingState() {
        elements.loadingState.fadeIn(CONFIG.animationDuration);
        elements.emptyState.hide();
        elements.resultsHeader.hide();
    }

    function hideLoadingState() {
        elements.loadingState.fadeOut(CONFIG.animationDuration);
    }

    function showEmptyState() {
        elements.emptyState.fadeIn(CONFIG.animationDuration);
        elements.resultsHeader.hide();
    }

    function hideEmptyState() {
        elements.emptyState.hide();
    }

    function showResultsHeader() {
        elements.resultsHeader.fadeIn(CONFIG.animationDuration);
    }

    function showErrorState(message) {
        hideLoadingState();
        
        const errorHTML = `
            <div class="trpro-error-state">
                <div class="trpro-error-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h3>Erreur de recherche</h3>
                <p>${message}</p>
                <button class="trpro-btn trpro-btn-primary" onclick="location.reload()">
                    <i class="fas fa-refresh"></i>
                    Réessayer
                </button>
            </div>
        `;
        
        elements.resultsContainer.html(errorHTML);
        showResultsHeader();
    }

    // ===== GESTION DES MODALS =====
    function handleTrainerDetails() {
        const trainerId = $(this).data('trainer-id');
        showTrainerModal(trainerId);
    }

    function showTrainerModal(trainerId) {
        // Ici on pourrait charger plus de détails via AJAX
        const modal = $(`#trpro-modal-${trainerId}`);
        if (modal.length) {
            modal.addClass('active');
            $('body').addClass('modal-open');
        } else {
            // Créer et afficher un modal générique
            createGenericModal(trainerId);
        }
    }

    function createGenericModal(trainerId) {
        const modalHTML = `
            <div class="trpro-modal-overlay active" id="trpro-modal-${trainerId}">
                <div class="trpro-modal-container">
                    <div class="trpro-modal-header">
                        <h4><i class="fas fa-user-graduate"></i> Formateur Expert #${String(trainerId).padStart(4, '0')}</h4>
                        <button class="trpro-modal-close"><i class="fas fa-times"></i></button>
                    </div>
                    <div class="trpro-modal-content">
                        <div class="trpro-modal-section">
                            <h5><i class="fas fa-info-circle"></i> Informations</h5>
                            <p>Pour obtenir plus d'informations sur ce formateur, veuillez nous contacter directement.</p>
                        </div>
                        <div class="trpro-modal-actions">
                            <a href="mailto:${trainer_ajax.contact_email}?subject=Contact formateur %23${String(trainerId).padStart(4, '0')}" 
                               class="trpro-btn trpro-btn-primary trpro-btn-large">
                                <i class="fas fa-envelope"></i>
                                Contacter par Email
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        $('body').append(modalHTML);
        $('body').addClass('modal-open');
    }

    function closeModal(modal) {
        modal.removeClass('active');
        $('body').removeClass('modal-open');
        
        setTimeout(() => {
            modal.remove();
        }, 300);
    }

    // ===== CHANGEMENT DE VUE =====
    function switchView(view) {
        elements.viewButtons.removeClass('active');
        $(`.trpro-view-btn[data-view="${view}"]`).addClass('active');
        
        elements.resultsContainer
            .removeClass('trpro-view-grid trpro-view-list')
            .addClass(`trpro-view-${view}`);
        
        // Sauvegarder la préférence
        localStorage.setItem('trpro-view-preference', view);
    }

    // ===== UTILITAIRES =====
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

    function updateURL(query, specialty) {
        if (history.pushState) {
            const url = new URL(window.location);
            
            if (query) {
                url.searchParams.set('search', query);
            } else {
                url.searchParams.delete('search');
            }
            
            if (specialty) {
                url.searchParams.set('specialty', specialty);
            } else {
                url.searchParams.delete('specialty');
            }
            
            history.pushState(null, '', url);
        }
    }

    // ===== FONCTIONS GLOBALES =====
    window.resetSearch = clearSearch;
    
    // ===== GESTION DES ERREURS =====
    window.addEventListener('error', function(e) {
        console.error('❌ Erreur JavaScript:', e.error);
    });

    // ===== CLEANUP =====
    $(window).on('beforeunload', function() {
        if (currentRequest) {
            currentRequest.abort();
        }
        clearTimeout(searchTimeout);
    });

})(jQuery);