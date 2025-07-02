<?php
/**
 * Template recherche formateurs MODERNE - Design Professionnel Noir/Jaune
 * 
 * Fichier: /wp-content/plugins/trainer-registration-plugin/public/partials/trainer-search.php
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="trpro-search-container" id="trpro-trainer-search">
    <!-- En-tête de recherche moderne -->
    <div class="trpro-search-header">
        <div class="trpro-search-hero">
            <h3>
                <i class="fas fa-search"></i>
                Recherche Intelligente
            </h3>
            <p>Trouvez le formateur expert parfaitement adapté à vos besoins</p>
        </div>
    </div>
    
    <!-- Formulaire de recherche principal -->
    <div class="trpro-search-main">
        <div class="trpro-search-form">
            <div class="trpro-search-input-wrapper">
                <div class="trpro-search-field">
                    <input type="text" 
                           id="trpro-trainer-search-input" 
                           placeholder="Rechercher par compétence, technologie, certification..."
                           autocomplete="off">
                    <div class="trpro-search-icon">
                        <i class="fas fa-search"></i>
                    </div>
                </div>
                
                <div class="trpro-filter-dropdown">
                    <select id="trpro-specialty-filter">
                        <option value="all">Toutes les spécialités</option>
                        <option value="administration-systeme">Administration Système</option>
                        <option value="reseaux">Réseaux & Infrastructure</option>
                        <option value="cloud">Cloud Computing</option>
                        <option value="devops">DevOps & CI/CD</option>
                        <option value="securite">Sécurité Informatique</option>
                        <option value="telecoms">Télécommunications</option>
                        <option value="developpement">Développement</option>
                        <option value="bases-donnees">Bases de Données</option>
                    </select>
                </div>
                
                <button type="button" id="trpro-search-trainers-btn" class="trpro-btn trpro-btn-primary trpro-btn-large">
                    <i class="fas fa-search"></i>
                    Rechercher
                </button>
            </div>
            
            <!-- Filtres avancés -->
            <div class="trpro-advanced-filters" id="trpro-advanced-filters" style="display: none;">
                <div class="trpro-filters-grid">
                    <div class="trpro-filter-group">
                        <label for="trpro-availability-filter">Disponibilité</label>
                        <select id="trpro-availability-filter">
                            <option value="">Toutes disponibilités</option>
                            <option value="temps-plein">Temps plein</option>
                            <option value="temps-partiel">Temps partiel</option>
                            <option value="ponctuel">Missions ponctuelles</option>
                            <option value="weekends">Weekends uniquement</option>
                            <option value="flexible">Flexible</option>
                        </select>
                    </div>
                    
                    <div class="trpro-filter-group">
                        <label for="trpro-experience-filter">Niveau d'expérience</label>
                        <select id="trpro-experience-filter">
                            <option value="">Tous niveaux</option>
                            <option value="junior">Junior (0-3 ans)</option>
                            <option value="intermediaire">Intermédiaire (3-7 ans)</option>
                            <option value="senior">Senior (7-15 ans)</option>
                            <option value="expert">Expert (15+ ans)</option>
                        </select>
                    </div>
                    
                    <div class="trpro-filter-group">
                        <label for="trpro-rate-filter">Tarif horaire</label>
                        <select id="trpro-rate-filter">
                            <option value="">Tous tarifs</option>
                            <option value="0-50">Moins de 50€/h</option>
                            <option value="50-80">50€ - 80€/h</option>
                            <option value="80-120">80€ - 120€/h</option>
                            <option value="120+">Plus de 120€/h</option>
                        </select>
                    </div>
                </div>
                
                <div class="trpro-filter-actions">
                    <button type="button" class="trpro-btn trpro-btn-secondary" id="trpro-reset-filters">
                        <i class="fas fa-times"></i>
                        Réinitialiser
                    </button>
                    <button type="button" class="trpro-btn trpro-btn-accent" id="trpro-apply-filters">
                        <i class="fas fa-filter"></i>
                        Appliquer les filtres
                    </button>
                </div>
            </div>
            
            <button type="button" class="trpro-toggle-advanced" id="trpro-toggle-advanced">
                <i class="fas fa-sliders-h"></i>
                <span>Filtres avancés</span>
                <i class="fas fa-chevron-down trpro-toggle-icon"></i>
            </button>
        </div>
    </div>
    
    <!-- Zone de résultats -->
    <div class="trpro-search-results-container">
        <div id="trpro-search-results" class="trpro-search-results">
            <!-- Placeholder initial -->
            <div class="trpro-search-placeholder">
                <div class="trpro-placeholder-content">
                    <div class="trpro-placeholder-icon">
                        <i class="fas fa-search"></i>
                        <div class="trpro-search-pulse"></div>
                    </div>
                    <h4>Commencez votre recherche</h4>
                    <p>Utilisez la barre de recherche ci-dessus pour trouver des formateurs experts dans votre domaine</p>
                </div>
            </div>
        </div>
        
        <!-- Loading state -->
        <div id="trpro-search-loading" class="trpro-loading-state" style="display: none;">
            <div class="trpro-loading-animation">
                <div class="trpro-spinner"></div>
                <div class="trpro-loading-dots">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
            <span>Recherche en cours...</span>
        </div>
    </div>
    
    <!-- Suggestions de recherche intelligentes -->
    <div class="trpro-search-suggestions">
        <div class="trpro-suggestions-header">
            <h4>
                <i class="fas fa-lightbulb"></i>
                Recherches populaires
            </h4>
            <p>Découvrez les compétences les plus demandées</p>
        </div>
        
        <div class="trpro-suggestion-categories">
            <!-- Catégorie Cloud -->
            <div class="trpro-suggestion-category">
                <h5>
                    <i class="fas fa-cloud"></i>
                    Cloud & Infrastructure
                </h5>
                <div class="trpro-suggestion-tags">
                    <button class="trpro-suggestion-tag" data-search="aws" data-category="cloud">
                        <i class="fab fa-aws"></i>
                        <span>AWS</span>
                        <div class="trpro-tag-popularity"></div>
                    </button>
                    <button class="trpro-suggestion-tag" data-search="kubernetes" data-category="cloud">
                        <i class="fas fa-dharmachakra"></i>
                        <span>Kubernetes</span>
                        <div class="trpro-tag-popularity"></div>
                    </button>
                    <button class="trpro-suggestion-tag" data-search="docker" data-category="cloud">
                        <i class="fab fa-docker"></i>
                        <span>Docker</span>
                        <div class="trpro-tag-popularity"></div>
                    </button>
                    <button class="trpro-suggestion-tag" data-search="azure" data-category="cloud">
                        <i class="fab fa-microsoft"></i>
                        <span>Azure</span>
                        <div class="trpro-tag-popularity"></div>
                    </button>
                </div>
            </div>
            
            <!-- Catégorie Sécurité -->
            <div class="trpro-suggestion-category">
                <h5>
                    <i class="fas fa-shield-alt"></i>
                    Sécurité & Protection
                </h5>
                <div class="trpro-suggestion-tags">
                    <button class="trpro-suggestion-tag" data-search="cybersécurité" data-category="securite">
                        <i class="fas fa-user-shield"></i>
                        <span>Cybersécurité</span>
                        <div class="trpro-tag-popularity"></div>
                    </button>
                    <button class="trpro-suggestion-tag" data-search="pentest" data-category="securite">
                        <i class="fas fa-bug"></i>
                        <span>Pentest</span>
                        <div class="trpro-tag-popularity"></div>
                    </button>
                    <button class="trpro-suggestion-tag" data-search="rgpd" data-category="securite">
                        <i class="fas fa-balance-scale"></i>
                        <span>RGPD</span>
                        <div class="trpro-tag-popularity"></div>
                    </button>
                    <button class="trpro-suggestion-tag" data-search="iso 27001" data-category="securite">
                        <i class="fas fa-certificate"></i>
                        <span>ISO 27001</span>
                        <div class="trpro-tag-popularity"></div>
                    </button>
                </div>
            </div>
            
            <!-- Catégorie DevOps -->
            <div class="trpro-suggestion-category">
                <h5>
                    <i class="fas fa-infinity"></i>
                    DevOps & Automatisation
                </h5>
                <div class="trpro-suggestion-tags">
                    <button class="trpro-suggestion-tag" data-search="jenkins" data-category="devops">
                        <i class="fas fa-cogs"></i>
                        <span>Jenkins</span>
                        <div class="trpro-tag-popularity"></div>
                    </button>
                    <button class="trpro-suggestion-tag" data-search="ansible" data-category="devops">
                        <i class="fas fa-robot"></i>
                        <span>Ansible</span>
                        <div class="trpro-tag-popularity"></div>
                    </button>
                    <button class="trpro-suggestion-tag" data-search="terraform" data-category="devops">
                        <i class="fas fa-layer-group"></i>
                        <span>Terraform</span>
                        <div class="trpro-tag-popularity"></div>
                    </button>
                    <button class="trpro-suggestion-tag" data-search="gitlab ci" data-category="devops">
                        <i class="fab fa-gitlab"></i>
                        <span>GitLab CI</span>
                        <div class="trpro-tag-popularity"></div>
                    </button>
                </div>
            </div>
            
            <!-- Catégorie Systèmes -->
            <div class="trpro-suggestion-category">
                <h5>
                    <i class="fas fa-server"></i>
                    Systèmes & Réseaux
                </h5>
                <div class="trpro-suggestion-tags">
                    <button class="trpro-suggestion-tag" data-search="linux" data-category="administration-systeme">
                        <i class="fab fa-linux"></i>
                        <span>Linux</span>
                        <div class="trpro-tag-popularity"></div>
                    </button>
                    <button class="trpro-suggestion-tag" data-search="cisco" data-category="reseaux">
                        <i class="fas fa-network-wired"></i>
                        <span>Cisco</span>
                        <div class="trpro-tag-popularity"></div>
                    </button>
                    <button class="trpro-suggestion-tag" data-search="vmware" data-category="administration-systeme">
                        <i class="fas fa-cube"></i>
                        <span>VMware</span>
                        <div class="trpro-tag-popularity"></div>
                    </button>
                    <button class="trpro-suggestion-tag" data-search="windows server" data-category="administration-systeme">
                        <i class="fab fa-windows"></i>
                        <span>Windows Server</span>
                        <div class="trpro-tag-popularity"></div>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Styles modernes pour la recherche de formateurs */
.trpro-search-container {
    max-width: 1000px;
    margin: 0 auto;
    padding: 0 var(--trpro-space-6);
}

/* En-tête de recherche */
.trpro-search-header {
    text-align: center;
    margin-bottom: var(--trpro-space-12);
}

.trpro-search-hero h3 {
    font-size: 2rem;
    font-weight: 600;
    color: var(--trpro-text-primary);
    margin-bottom: var(--trpro-space-3);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: var(--trpro-space-3);
}

.trpro-search-hero h3 i {
    color: var(--trpro-accent);
    font-size: 1.8rem;
}

.trpro-search-hero p {
    color: var(--trpro-text-secondary);
    font-size: 1.125rem;
    max-width: 500px;
    margin: 0 auto;
    line-height: 1.6;
}

/* Formulaire de recherche principal */
.trpro-search-main {
    background: var(--trpro-bg-primary);
    border: 1px solid var(--trpro-border);
    border-radius: var(--trpro-radius-xl);
    padding: var(--trpro-space-10);
    margin-bottom: var(--trpro-space-12);
    box-shadow: var(--trpro-shadow-lg);
    position: relative;
}

.trpro-search-input-wrapper {
    display: grid;
    grid-template-columns: 1fr auto auto;
    gap: var(--trpro-space-4);
    align-items: center;
    margin-bottom: var(--trpro-space-6);
}

.trpro-search-field {
    position: relative;
    flex: 1;
}

.trpro-search-field input {
    width: 100%;
    padding: var(--trpro-space-4) var(--trpro-space-5);
    padding-right: 50px;
    border: 2px solid var(--trpro-border);
    border-radius: var(--trpro-radius-lg);
    font-size: 1rem;
    font-family: var(--trpro-font-family);
    transition: var(--trpro-transition);
    background: var(--trpro-bg-primary);
    color: var(--trpro-text-primary);
}

.trpro-search-field input:focus {
    outline: none;
    border-color: var(--trpro-accent);
    box-shadow: 0 0 0 3px rgba(251, 191, 36, 0.1);
}

.trpro-search-field input::placeholder {
    color: var(--trpro-text-tertiary);
}

.trpro-search-icon {
    position: absolute;
    right: var(--trpro-space-4);
    top: 50%;
    transform: translateY(-50%);
    color: var(--trpro-text-tertiary);
    pointer-events: none;
    font-size: 1.1rem;
}

.trpro-filter-dropdown select {
    padding: var(--trpro-space-4) var(--trpro-space-5);
    border: 2px solid var(--trpro-border);
    border-radius: var(--trpro-radius-lg);
    background: var(--trpro-bg-primary);
    font-size: 1rem;
    font-family: var(--trpro-font-family);
    color: var(--trpro-text-primary);
    min-width: 200px;
    transition: var(--trpro-transition);
}

.trpro-filter-dropdown select:focus {
    outline: none;
    border-color: var(--trpro-accent);
    box-shadow: 0 0 0 3px rgba(251, 191, 36, 0.1);
}

/* Filtres avancés */
.trpro-advanced-filters {
    background: var(--trpro-bg-secondary);
    border-radius: var(--trpro-radius-lg);
    padding: var(--trpro-space-8);
    margin-top: var(--trpro-space-6);
    border: 1px solid var(--trpro-border);
}

.trpro-filters-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: var(--trpro-space-6);
    margin-bottom: var(--trpro-space-6);
}

.trpro-filter-group label {
    display: block;
    margin-bottom: var(--trpro-space-2);
    font-weight: 600;
    color: var(--trpro-text-primary);
    font-size: 0.9rem;
}

.trpro-filter-group select {
    width: 100%;
    padding: var(--trpro-space-3) var(--trpro-space-4);
    border: 1px solid var(--trpro-border);
    border-radius: var(--trpro-radius-sm);
    background: var(--trpro-bg-primary);
    font-size: 0.9rem;
    transition: var(--trpro-transition);
}

.trpro-filter-group select:focus {
    outline: none;
    border-color: var(--trpro-accent);
    box-shadow: 0 0 0 3px rgba(251, 191, 36, 0.1);
}

.trpro-filter-actions {
    display: flex;
    gap: var(--trpro-space-3);
    justify-content: flex-end;
}

.trpro-toggle-advanced {
    display: flex;
    align-items: center;
    gap: var(--trpro-space-2);
    padding: var(--trpro-space-3) var(--trpro-space-4);
    border: 1px solid var(--trpro-border);
    background: var(--trpro-bg-secondary);
    border-radius: var(--trpro-radius-lg);
    cursor: pointer;
    transition: var(--trpro-transition);
    font-weight: 500;
    color: var(--trpro-text-secondary);
    margin-top: var(--trpro-space-4);
}

.trpro-toggle-advanced:hover {
    background: var(--trpro-accent);
    color: var(--trpro-primary);
    border-color: var(--trpro-accent);
}

.trpro-toggle-icon {
    transition: var(--trpro-transition);
}

.trpro-toggle-advanced.expanded .trpro-toggle-icon {
    transform: rotate(180deg);
}

/* Zone de résultats */
.trpro-search-results-container {
    position: relative;
    min-height: 400px;
    margin-bottom: var(--trpro-space-16);
}

.trpro-search-results {
    margin-bottom: var(--trpro-space-12);
}

/* Placeholder */
.trpro-search-placeholder {
    text-align: center;
    padding: var(--trpro-space-20) var(--trpro-space-6);
    background: var(--trpro-bg-secondary);
    border-radius: var(--trpro-radius-lg);
    border: 2px dashed var(--trpro-border);
    position: relative;
    overflow: hidden;
}

.trpro-placeholder-content {
    position: relative;
    z-index: 1;
}

.trpro-placeholder-icon {
    position: relative;
    display: inline-block;
    margin-bottom: var(--trpro-space-6);
    font-size: 4rem;
    color: var(--trpro-accent);
}

.trpro-search-pulse {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 100%;
    height: 100%;
    border: 2px solid var(--trpro-accent);
    border-radius: 50%;
    animation: pulse 2s ease-in-out infinite;
    opacity: 0.3;
}

@keyframes pulse {
    0% { transform: translate(-50%, -50%) scale(1); opacity: 0.3; }
    50% { transform: translate(-50%, -50%) scale(1.2); opacity: 0.1; }
    100% { transform: translate(-50%, -50%) scale(1); opacity: 0.3; }
}

.trpro-search-placeholder h4 {
    font-size: 1.5rem;
    color: var(--trpro-text-primary);
    margin-bottom: var(--trpro-space-3);
    font-weight: 600;
}

.trpro-search-placeholder p {
    color: var(--trpro-text-secondary);
    font-size: 1.125rem;
    max-width: 400px;
    margin: 0 auto;
    line-height: 1.6;
}

/* Loading state */
.trpro-loading-state {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.95);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    border-radius: var(--trpro-radius-lg);
    backdrop-filter: blur(4px);
    z-index: 10;
}

.trpro-loading-animation {
    margin-bottom: var(--trpro-space-4);
    position: relative;
}

.trpro-loading-state .trpro-spinner {
    margin-bottom: var(--trpro-space-4);
}

.trpro-loading-dots {
    display: flex;
    gap: 6px;
}

.trpro-loading-dots span {
    width: 8px;
    height: 8px;
    background: var(--trpro-accent);
    border-radius: 50%;
    animation: bounce 1.4s ease-in-out infinite both;
}

.trpro-loading-dots span:nth-child(1) { animation-delay: -0.32s; }
.trpro-loading-dots span:nth-child(2) { animation-delay: -0.16s; }

@keyframes bounce {
    0%, 80%, 100% { transform: scale(0); }
    40% { transform: scale(1); }
}

.trpro-loading-state span {
    color: var(--trpro-text-secondary);
    font-weight: 500;
    font-size: 1rem;
}

/* Suggestions intelligentes */
.trpro-search-suggestions {
    background: var(--trpro-bg-primary);
    border: 1px solid var(--trpro-border);
    border-radius: var(--trpro-radius-xl);
    padding: var(--trpro-space-12);
    box-shadow: var(--trpro-shadow-md);
}

.trpro-suggestions-header {
    text-align: center;
    margin-bottom: var(--trpro-space-10);
}

.trpro-suggestions-header h4 {
    font-size: 1.5rem;
    font-weight: 600;
    color: var(--trpro-text-primary);
    margin-bottom: var(--trpro-space-2);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: var(--trpro-space-2);
}

.trpro-suggestions-header h4 i {
    color: var(--trpro-accent);
}

.trpro-suggestions-header p {
    color: var(--trpro-text-secondary);
    font-size: 1rem;
}

.trpro-suggestion-categories {
    display: grid;
    gap: var(--trpro-space-8);
}

.trpro-suggestion-category {
    background: var(--trpro-bg-secondary);
    border-radius: var(--trpro-radius-lg);
    padding: var(--trpro-space-6);
    border: 1px solid var(--trpro-border);
}

.trpro-suggestion-category h5 {
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--trpro-text-primary);
    margin-bottom: var(--trpro-space-4);
    display: flex;
    align-items: center;
    gap: var(--trpro-space-2);
}

.trpro-suggestion-category h5 i {
    color: var(--trpro-accent);
    font-size: 1rem;
}

.trpro-suggestion-tags {
    display: flex;
    flex-wrap: wrap;
    gap: var(--trpro-space-3);
}

.trpro-suggestion-tag {
    display: flex;
    align-items: center;
    gap: var(--trpro-space-2);
    padding: var(--trpro-space-3) var(--trpro-space-4);
    background: var(--trpro-bg-primary);
    border: 1px solid var(--trpro-border);
    border-radius: var(--trpro-radius-lg);
    color: var(--trpro-text-secondary);
    font-weight: 500;
    cursor: pointer;
    transition: var(--trpro-transition);
    text-decoration: none;
    position: relative;
    overflow: hidden;
}

.trpro-suggestion-tag:hover {
    border-color: var(--trpro-accent);
    background: var(--trpro-bg-accent);
    color: var(--trpro-primary);
    transform: translateY(-2px);
    box-shadow: var(--trpro-shadow-md);
}

.trpro-suggestion-tag i {
    font-size: 1rem;
    color: var(--trpro-accent);
}

.trpro-tag-popularity {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: var(--trpro-accent);
    opacity: 0.3;
}

/* Effet de popularité différent pour chaque tag */
.trpro-suggestion-tag:nth-child(1) .trpro-tag-popularity { width: 90%; }
.trpro-suggestion-tag:nth-child(2) .trpro-tag-popularity { width: 75%; }
.trpro-suggestion-tag:nth-child(3) .trpro-tag-popularity { width: 60%; }
.trpro-suggestion-tag:nth-child(4) .trpro-tag-popularity { width: 45%; }

/* Responsive */
@media (max-width: 1024px) {
    .trpro-search-input-wrapper {
        grid-template-columns: 1fr;
        gap: var(--trpro-space-4);
    }
    
    .trpro-filters-grid {
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    }
}

@media (max-width: 768px) {
    .trpro-search-container {
        padding: 0 var(--trpro-space-4);
    }
    
    .trpro-search-hero h3 {
        font-size: 1.75rem;
        flex-direction: column;
        gap: var(--trpro-space-2);
    }
    
    .trpro-search-main {
        padding: var(--trpro-space-6);
    }
    
    .trpro-filter-dropdown select {
        min-width: 100%;
    }
    
    .trpro-filters-grid {
        grid-template-columns: 1fr;
    }
    
    .trpro-filter-actions {
        flex-direction: column;
    }
    
    .trpro-suggestion-tags {
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .trpro-search-hero h3 {
        font-size: 1.5rem;
    }
    
    .trpro-suggestion-tag {
        flex: 1;
        min-width: calc(50% - 6px);
        justify-content: center;
    }
    
    .trpro-placeholder-icon {
        font-size: 3rem;
    }
    
    .trpro-search-placeholder h4 {
        font-size: 1.25rem;
    }
}

/* Animations d'entrée */
.trpro-suggestion-category {
    opacity: 0;
    transform: translateY(20px);
    animation: slideInUp 0.6s ease-out forwards;
}

.trpro-suggestion-category:nth-child(1) { animation-delay: 0.1s; }
.trpro-suggestion-category:nth-child(2) { animation-delay: 0.2s; }
.trpro-suggestion-category:nth-child(3) { animation-delay: 0.3s; }
.trpro-suggestion-category:nth-child(4) { animation-delay: 0.4s; }

@keyframes slideInUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Message d'absence de résultats */
.trpro-no-results {
    text-align: center;
    padding: var(--trpro-space-16) var(--trpro-space-6);
    background: var(--trpro-bg-secondary);
    border-radius: var(--trpro-radius-lg);
    border: 1px solid var(--trpro-border);
}

.trpro-no-results .trpro-empty-icon {
    font-size: 4rem;
    color: var(--trpro-text-light);
    margin-bottom: var(--trpro-space-6);
}

.trpro-no-results h3 {
    font-size: 1.5rem;
    color: var(--trpro-text-primary);
    margin-bottom: var(--trpro-space-3);
    font-weight: 600;
}

.trpro-no-results p {
    color: var(--trpro-text-secondary);
    font-size: 1.125rem;
    line-height: 1.6;
    max-width: 400px;
    margin: 0 auto;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // ===== ÉLÉMENTS DOM =====
    const searchInput = document.getElementById('trpro-trainer-search-input');
    const searchBtn = document.getElementById('trpro-search-trainers-btn');
    const specialtyFilter = document.getElementById('trpro-specialty-filter');
    const toggleAdvanced = document.getElementById('trpro-toggle-advanced');
    const advancedFilters = document.getElementById('trpro-advanced-filters');
    const resetFilters = document.getElementById('trpro-reset-filters');
    const applyFilters = document.getElementById('trpro-apply-filters');
    const searchResults = document.getElementById('trpro-search-results');
    const searchLoading = document.getElementById('trpro-search-loading');
    
    // ===== GESTION DES SUGGESTIONS =====
    const suggestionTags = document.querySelectorAll('.trpro-suggestion-tag');
    
    suggestionTags.forEach(tag => {
        tag.addEventListener('click', function(e) {
            e.preventDefault();
            const searchTerm = this.dataset.search;
            const category = this.dataset.category;
            
            // Remplir les champs
            searchInput.value = searchTerm;
            if (category && category !== 'all') {
                specialtyFilter.value = category;
            }
            
            // Effet visuel
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = '';
                // Déclencher la recherche
                performSearch();
            }, 150);
        });
    });
    
    // ===== GESTION DES FILTRES AVANCÉS =====
    toggleAdvanced.addEventListener('click', function() {
        const isExpanded = this.classList.contains('expanded');
        
        if (isExpanded) {
            // Fermer
            advancedFilters.style.display = 'none';
            this.classList.remove('expanded');
            this.querySelector('span').textContent = 'Filtres avancés';
        } else {
            // Ouvrir
            advancedFilters.style.display = 'block';
            this.classList.add('expanded');
            this.querySelector('span').textContent = 'Masquer les filtres';
            
            // Animation d'ouverture
            advancedFilters.style.opacity = '0';
            advancedFilters.style.transform = 'translateY(-10px)';
            
            requestAnimationFrame(() => {
                advancedFilters.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                advancedFilters.style.opacity = '1';
                advancedFilters.style.transform = 'translateY(0)';
            });
        }
    });
    
    // ===== RESET DES FILTRES =====
    resetFilters.addEventListener('click', function() {
        // Réinitialiser tous les champs
        searchInput.value = '';
        specialtyFilter.value = 'all';
        document.getElementById('trpro-availability-filter').value = '';
        document.getElementById('trpro-experience-filter').value = '';
        document.getElementById('trpro-rate-filter').value = '';
        
        // Afficher le placeholder
        showSearchPlaceholder();
        
        // Effet visuel
        this.style.transform = 'scale(0.95)';
        setTimeout(() => {
            this.style.transform = '';
        }, 150);
    });
    
    // ===== RECHERCHE =====
    function performSearch() {
        const query = searchInput.value.trim();
        const specialty = specialtyFilter.value;
        const availability = document.getElementById('trpro-availability-filter').value;
        const experience = document.getElementById('trpro-experience-filter').value;
        const rate = document.getElementById('trpro-rate-filter').value;
        
        if (!query && specialty === 'all' && !availability && !experience && !rate) {
            showSearchPlaceholder();
            return;
        }
        
        // Afficher le loading
        showSearchLoading();
        
        // Simuler une recherche AJAX
        setTimeout(() => {
            // Ici vous intégreriez votre logique de recherche AJAX réelle
            simulateSearchResults(query, specialty, availability, experience, rate);
        }, 1500);
    }
    
    function showSearchLoading() {
        searchResults.style.display = 'none';
        searchLoading.style.display = 'flex';
    }
    
    function showSearchPlaceholder() {
        searchLoading.style.display = 'none';
        searchResults.innerHTML = `
            <div class="trpro-search-placeholder">
                <div class="trpro-placeholder-content">
                    <div class="trpro-placeholder-icon">
                        <i class="fas fa-search"></i>
                        <div class="trpro-search-pulse"></div>
                    </div>
                    <h4>Commencez votre recherche</h4>
                    <p>Utilisez la barre de recherche ci-dessus pour trouver des formateurs experts dans votre domaine</p>
                </div>
            </div>
        `;
        searchResults.style.display = 'block';
    }
    
    function simulateSearchResults(query, specialty, availability, experience, rate) {
        searchLoading.style.display = 'none';
        
        // Simulation de résultats
        const hasResults = Math.random() > 0.3; // 70% chance d'avoir des résultats
        
        if (hasResults) {
            const resultCount = Math.floor(Math.random() * 12) + 1;
            searchResults.innerHTML = `
                <div class="trpro-search-results-header">
                    <h4>
                        <i class="fas fa-check-circle" style="color: var(--trpro-success);"></i>
                        ${resultCount} formateur${resultCount > 1 ? 's' : ''} trouvé${resultCount > 1 ? 's' : ''}
                    </h4>
                    <p>Résultats pour "${query || specialty}" ${specialty !== 'all' ? `dans ${specialty}` : ''}</p>
                </div>
                <div class="trpro-trainers-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: var(--trpro-space-6); margin-top: var(--trpro-space-6);">
                    ${Array.from({length: Math.min(resultCount, 6)}, (_, i) => `
                        <div class="trpro-trainer-card" style="padding: var(--trpro-space-6); border: 1px solid var(--trpro-border); border-radius: var(--trpro-radius-lg); background: var(--trpro-bg-primary); box-shadow: var(--trpro-shadow-sm);">
                            <div style="text-align: center; padding: var(--trpro-space-4);">
                                <div style="width: 60px; height: 60px; background: var(--trpro-accent); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto var(--trpro-space-3); color: var(--trpro-primary); font-size: 1.5rem;">
                                    <i class="fas fa-user"></i>
                                </div>
                                <h5 style="color: var(--trpro-text-primary); margin-bottom: var(--trpro-space-2);">Formateur Expert #${String(i + 1).padStart(4, '0')}</h5>
                                <p style="color: var(--trpro-text-secondary); font-size: 0.9rem; margin-bottom: var(--trpro-space-4);">Spécialiste ${specialty !== 'all' ? specialty.replace('-', ' ') : 'IT'}</p>
                                <div style="display: flex; gap: var(--trpro-space-2);">
                                    <button class="trpro-btn trpro-btn-primary trpro-btn-small" style="flex: 1;">
                                        <i class="fas fa-envelope"></i>
                                        Contacter
                                    </button>
                                    <button class="trpro-btn trpro-btn-outline trpro-btn-small">
                                        <i class="fas fa-info"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    `).join('')}
                </div>
                ${resultCount > 6 ? `
                    <div style="text-align: center; margin-top: var(--trpro-space-8);">
                        <button class="trpro-btn trpro-btn-accent">
                            <i class="fas fa-plus"></i>
                            Voir les ${resultCount - 6} autres résultats
                        </button>
                    </div>
                ` : ''}
            `;
        } else {
            searchResults.innerHTML = `
                <div class="trpro-no-results">
                    <div class="trpro-empty-icon">
                        <i class="fas fa-search-minus"></i>
                    </div>
                    <h3>Aucun résultat trouvé</h3>
                    <p>Essayez de modifier vos critères de recherche ou utilisez des termes plus généraux.</p>
                    <div style="margin-top: var(--trpro-space-6);">
                        <button class="trpro-btn trpro-btn-primary" onclick="document.getElementById('trpro-reset-filters').click()">
                            <i class="fas fa-redo"></i>
                            Réinitialiser la recherche
                        </button>
                    </div>
                </div>
            `;
        }
        
        searchResults.style.display = 'block';
        
        // Animer l'apparition des résultats
        searchResults.style.opacity = '0';
        searchResults.style.transform = 'translateY(20px)';
        
        requestAnimationFrame(() => {
            searchResults.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
            searchResults.style.opacity = '1';
            searchResults.style.transform = 'translateY(0)';
        });
    }
    
    // ===== ÉVÉNEMENTS =====
    searchBtn.addEventListener('click', performSearch);
    applyFilters.addEventListener('click', performSearch);
    
    // Recherche en temps réel avec debounce
    let searchTimeout;
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            if (this.value.length >= 3 || this.value.length === 0) {
                performSearch();
            }
        }, 500);
    });
    
    // Recherche sur Enter
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            performSearch();
        }
    });
    
    // Recherche sur changement de spécialité
    specialtyFilter.addEventListener('change', performSearch);
    
    // ===== AUTO-COMPLÉTION =====
    const suggestions = [
        'AWS', 'Azure', 'Kubernetes', 'Docker', 'Linux', 'Cisco', 'Cybersécurité',
        'DevOps', 'Jenkins', 'Ansible', 'Terraform', 'Python', 'VMware', 'RGPD',
        'ISO 27001', 'Pentest', 'Windows Server', 'GitLab CI', 'Monitoring'
    ];
    
    searchInput.addEventListener('input', function() {
        const value = this.value.toLowerCase();
        if (value.length >= 2) {
            const matches = suggestions.filter(suggestion => 
                suggestion.toLowerCase().includes(value)
            );
            // Ici vous pourriez afficher une dropdown d'auto-complétion
        }
    });
    
    // ===== ANIMATION D'ENTRÉE =====
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, { threshold: 0.1 });
    
    // Observer les catégories de suggestions
    document.querySelectorAll('.trpro-suggestion-category').forEach(category => {
        category.style.opacity = '0';
        category.style.transform = 'translateY(20px)';
        category.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(category);
    });
    
    // ===== INITIALISATION =====
    showSearchPlaceholder();
});
</script>