<?php
/**
 * Template de liste moderne des formateurs avec anonymisation - VERSION COMPLÈTE
 * 
 * Fichier: public/partials/trainer-list-modern.php
 */

if (!defined('ABSPATH')) {
    exit;
}

// Fonction d'anonymisation
function get_anonymized_name($first_name, $last_name) {
    if (empty($last_name) || empty($first_name)) {
        return 'Formateur Expert';
    }
    return strtoupper(substr($last_name, 0, 1)) . '. ' . $first_name;
}
?>

<div class="trpro-search-container">
    
    <!-- Header de recherche avancée -->
    <div class="trpro-search-header">
        <h2 class="trpro-search-title">
            <i class="fas fa-search"></i>
            Trouvez Votre Formateur Expert
        </h2>
        <p class="trpro-search-subtitle">
            Recherchez parmi <?php echo $total_trainers; ?> formateurs spécialisés dans toute la France
        </p>
    </div>

    <!-- Formulaire de recherche avancée -->
    <div class="trpro-search-form-modern">
        <div class="trpro-search-main">
            <div class="trpro-search-input-group">
                <input type="text" 
                       id="trpro-live-search" 
                       placeholder="Rechercher par compétence, technologie, certification..."
                       class="trpro-search-input">
                <button type="button" class="trpro-search-clear" style="display: none;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <button type="button" class="trpro-search-btn">
                <i class="fas fa-search"></i>
                <span>Rechercher</span>
            </button>
        </div>
        
        <!-- Filtres avancés -->
        <div class="trpro-filters-section">
            <div class="trpro-filters-row">
                <div class="trpro-filter-group">
                    <label for="trpro-specialty-filter">Spécialité</label>
                    <select id="trpro-specialty-filter" class="trpro-filter-select">
                        <option value="">Toutes les spécialités</option>
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
                
                <!-- ✅ NOUVEAU : Filtre par région -->
                <div class="trpro-filter-group">
                    <label for="trpro-region-filter">Zone d'intervention</label>
                    <select id="trpro-region-filter" class="trpro-filter-select">
                        <option value="">Toutes les zones</option>
                        <option value="ile-de-france">Île-de-France</option>
                        <option value="auvergne-rhone-alpes">Auvergne-Rhône-Alpes</option>
                        <option value="nouvelle-aquitaine">Nouvelle-Aquitaine</option>
                        <option value="occitanie">Occitanie</option>
                        <option value="hauts-de-france">Hauts-de-France</option>
                        <option value="grand-est">Grand Est</option>
                        <option value="provence-alpes-cote-azur">Provence-Alpes-Côte d'Azur</option>
                        <option value="pays-de-la-loire">Pays de la Loire</option>
                        <option value="bretagne">Bretagne</option>
                        <option value="normandie">Normandie</option>
                        <option value="bourgogne-franche-comte">Bourgogne-Franche-Comté</option>
                        <option value="centre-val-de-loire">Centre-Val de Loire</option>
                        <option value="corse">Corse</option>
                        <option value="outre-mer">Outre-mer (DOM-TOM)</option>
                        <option value="europe">Europe (hors France)</option>
                        <option value="international">International</option>
                        <option value="distanciel">Formation à distance</option>
                    </select>
                </div>
                
                <div class="trpro-filter-group">
                    <label for="trpro-availability-filter">Disponibilité</label>
                    <select id="trpro-availability-filter" class="trpro-filter-select">
                        <option value="">Toutes</option>
                        <option value="temps-plein">Temps plein</option>
                        <option value="temps-partiel">Temps partiel</option>
                        <option value="ponctuel">Missions ponctuelles</option>
                        <option value="sur-demande">Sur demande</option>
                    </select>
                </div>
                
                <div class="trpro-filter-group">
                    <label for="trpro-experience-filter">Expérience</label>
                    <select id="trpro-experience-filter" class="trpro-filter-select">
                        <option value="">Tous niveaux</option>
                        <option value="junior">Junior (< 3 ans)</option>
                        <option value="intermediaire">Intermédiaire (3-7 ans)</option>
                        <option value="senior">Senior (7-15 ans)</option>
                        <option value="expert">Expert (15+ ans)</option>
                    </select>
                </div>
            </div>
        </div>
        
        <!-- Tags de suggestions populaires -->
        <div class="trpro-popular-tags">
            <span class="trpro-tags-label">Recherches populaires :</span>
            <button class="trpro-tag" data-search="DevOps" data-category="devops">DevOps</button>
            <button class="trpro-tag" data-search="Cloud AWS" data-category="cloud">Cloud AWS</button>
            <button class="trpro-tag" data-search="Cybersécurité" data-category="securite">Cybersécurité</button>
            <button class="trpro-tag" data-search="Kubernetes" data-category="devops">Kubernetes</button>
            <button class="trpro-tag" data-search="Python" data-category="developpement">Python</button>
            <button class="trpro-tag" data-search="Distanciel" data-category="">Formation à distance</button>
        </div>
    </div>

    <!-- États de chargement et messages -->
    <div id="trpro-search-loading" class="trpro-search-loading" style="display: none;">
        <div class="trpro-loading-spinner">
            <i class="fas fa-spinner fa-spin"></i>
        </div>
        <p>Recherche en cours...</p>
    </div>

    <div id="trpro-empty-state" class="trpro-empty-state" style="display: none;">
        <div class="trpro-empty-icon">
            <i class="fas fa-search-minus"></i>
        </div>
        <h3>Aucun formateur trouvé</h3>
        <p>Essayez de modifier vos critères de recherche ou explorez d'autres spécialités.</p>
        <button class="trpro-btn trpro-btn-primary" onclick="resetSearch()">
            <i class="fas fa-refresh"></i>
            Réinitialiser la recherche
        </button>
    </div>

    <!-- Header des résultats -->
    <div id="trpro-results-header" class="trpro-results-header">
        <div class="trpro-results-info">
            <h3 id="trpro-results-title">Nos Formateurs Experts</h3>
            <p id="trpro-results-count"><?php echo $total_trainers; ?> formateur<?php echo $total_trainers > 1 ? 's' : ''; ?> disponible<?php echo $total_trainers > 1 ? 's' : ''; ?></p>
        </div>
        
        <div class="trpro-view-controls">
            <div class="trpro-view-switcher">
                <button class="trpro-view-btn active" data-view="grid" title="Vue grille">
                    <i class="fas fa-th"></i>
                </button>
                <button class="trpro-view-btn" data-view="list" title="Vue liste">
                    <i class="fas fa-list"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Grille des formateurs avec anonymisation -->
    <div id="trpro-trainers-grid" class="trpro-trainers-grid trpro-view-grid">
        <?php if (!empty($trainers)): ?>
            <?php foreach ($trainers as $trainer): ?>
                <?php 
                $trainer_id = str_pad($trainer->id, 4, '0', STR_PAD_LEFT);
                $specialties = explode(', ', $trainer->specialties);
                $intervention_regions = !empty($trainer->intervention_regions) ? explode(', ', $trainer->intervention_regions) : array();
                $upload_dir = wp_upload_dir();
                $display_name = get_anonymized_name($trainer->first_name, $trainer->last_name); // ✅ ANONYMISATION
                
                // Icons par spécialité
                $specialty_icons = array(
                    'administration-systeme' => 'fas fa-server',
                    'reseaux' => 'fas fa-network-wired',
                    'cloud' => 'fab fa-aws',
                    'devops' => 'fas fa-infinity',
                    'securite' => 'fas fa-shield-alt',
                    'telecoms' => 'fas fa-satellite-dish',
                    'developpement' => 'fas fa-code',
                    'bases-donnees' => 'fas fa-database'
                );
                ?>
                
                <article class="trpro-trainer-card-modern" data-trainer-id="<?php echo $trainer->id; ?>">
                    <div class="trpro-card-header">
                        <div class="trpro-trainer-avatar">
                            <?php if (!empty($trainer->photo_file)): ?>
                                <img src="<?php echo esc_url($upload_dir['baseurl'] . '/' . $trainer->photo_file); ?>" 
                                     alt="Photo du formateur #<?php echo $trainer_id; ?>" 
                                     loading="lazy">
                            <?php else: ?>
                                <div class="trpro-avatar-placeholder">
                                    <i class="fas fa-user-graduate"></i>
                                </div>
                            <?php endif; ?>
                            <div class="trpro-status-badge trpro-badge-verified">
                                <i class="fas fa-check-circle"></i>
                                <span>Vérifié</span>
                            </div>
                        </div>
                        
                        <div class="trpro-verification-badges">
                            <div class="trpro-badge trpro-verified" title="Profil vérifié">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <?php if (!empty($trainer->cv_file)): ?>
                                <div class="trpro-badge trpro-cv-badge" title="CV disponible">
                                    <i class="fas fa-file-pdf"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="trpro-card-body">
                        <div class="trpro-trainer-identity">
                            <h3 class="trpro-trainer-title">
                                <?php echo esc_html($display_name); ?> <!-- ✅ NOM ANONYMISÉ -->
                                <span class="trpro-trainer-id">#<?php echo $trainer_id; ?></span>
                            </h3>
                            <?php if (!empty($trainer->company)): ?>
                                <div class="trpro-trainer-company">
                                    <i class="fas fa-building"></i>
                                    <span><?php echo esc_html($trainer->company); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Spécialités avec icônes -->
                        <div class="trpro-specialties-section">
                            <div class="trpro-specialties-grid">
                                <?php 
                                $displayed_specialties = array_slice($specialties, 0, 3);
                                foreach ($displayed_specialties as $specialty): 
                                    $specialty = trim($specialty);
                                    $icon = isset($specialty_icons[$specialty]) ? $specialty_icons[$specialty] : 'fas fa-cog';
                                    $label = ucwords(str_replace('-', ' ', $specialty));
                                ?>
                                    <div class="trpro-specialty-item">
                                        <i class="<?php echo $icon; ?>"></i>
                                        <span><?php echo esc_html($label); ?></span>
                                    </div>
                                <?php endforeach; ?>
                                
                                <?php if (count($specialties) > 3): ?>
                                    <div class="trpro-specialty-item trpro-specialty-more">
                                        <i class="fas fa-plus"></i>
                                        <span>+<?php echo count($specialties) - 3; ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- ✅ NOUVEAU : Zones d'intervention -->
                        <?php if (!empty($intervention_regions)): ?>
                            <div class="trpro-regions-section">
                                <div class="trpro-regions-list">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span class="trpro-regions-text">
                                        <?php 
                                        $displayed_regions = array_slice($intervention_regions, 0, 2);
                                        $region_names = array();
                                        foreach ($displayed_regions as $region) {
                                            $region = trim($region);
                                            $region_names[] = ucwords(str_replace('-', ' ', $region));
                                        }
                                        echo esc_html(implode(', ', $region_names));
                                        
                                        if (count($intervention_regions) > 2) {
                                            echo ' <span class="trpro-regions-more">+' . (count($intervention_regions) - 2) . '</span>';
                                        }
                                        ?>
                                    </span>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Aperçu expérience -->
                        <?php if (!empty($trainer->experience)): ?>
                            <div class="trpro-experience-preview">
                                <div class="trpro-experience-text">
                                    <?php 
                                    $experience_preview = substr($trainer->experience, 0, 120);
                                    if (strlen($trainer->experience) > 120) {
                                        $experience_preview .= '...';
                                    }
                                    echo esc_html($experience_preview);
                                    ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Métadonnées -->
                        <div class="trpro-trainer-meta">
                            <?php if (!empty($trainer->availability)): ?>
                                <div class="trpro-meta-item">
                                    <i class="fas fa-calendar-check"></i>
                                    <span><?php echo esc_html(ucwords(str_replace('-', ' ', $trainer->availability))); ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($trainer->hourly_rate)): ?>
                                <div class="trpro-meta-item">
                                    <i class="fas fa-euro-sign"></i>
                                    <span><?php echo esc_html($trainer->hourly_rate); ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <div class="trpro-meta-item">
                                <i class="fas fa-calendar-plus"></i>
                                <span>Inscrit le <?php echo date('m/Y', strtotime($trainer->created_at)); ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Actions -->
                    <div class="trpro-card-footer">
                        <div class="trpro-action-buttons">
                            <a href="mailto:<?php echo get_option('trainer_contact_email', get_option('admin_email')); ?>?subject=Contact formateur %23<?php echo $trainer_id; ?>" 
                               class="trpro-btn trpro-btn-primary">
                                <i class="fas fa-envelope"></i>
                                <span>Contacter</span>
                            </a>
                            <button class="trpro-btn trpro-btn-outline trpro-btn-details" data-trainer-id="<?php echo $trainer->id; ?>">
                                <i class="fas fa-user"></i>
                                <span>Voir le profil</span>
                            </button>
                        </div>
                        
                        <?php if (!empty($trainer->linkedin_url)): ?>
                            <div class="trpro-additional-links">
                                <a href="<?php echo esc_url($trainer->linkedin_url); ?>" 
                                   target="_blank" 
                                   class="trpro-social-link" 
                                   title="Voir le profil LinkedIn">
                                    <i class="fab fa-linkedin"></i>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Indicateur de popularité (décoratif) -->
                    <div class="trpro-popularity-indicator">
                        <div class="trpro-popularity-bar" style="width: <?php echo rand(60, 95); ?>%;"></div>
                    </div>
                </article>
                
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if ($atts['show_pagination'] === 'true' && $total_trainers > $atts['per_page']): ?>
        <div class="trpro-pagination">
            <?php
            $total_pages = ceil($total_trainers / $atts['per_page']);
            $current_page = max(1, get_query_var('paged', 1));
            
            // Pagination WordPress standard adaptée
            $pagination_args = array(
                'base' => get_pagenum_link(1) . '%_%',
                'format' => 'page/%#%/',
                'current' => $current_page,
                'total' => $total_pages,
                'prev_text' => '<i class="fas fa-chevron-left"></i> Précédent',
                'next_text' => 'Suivant <i class="fas fa-chevron-right"></i>',
                'type' => 'array'
            );
            
            $pagination_links = paginate_links($pagination_args);
            
            if ($pagination_links) {
                echo '<nav class="trpro-pagination-nav">';
                echo '<ul class="trpro-pagination-list">';
                foreach ($pagination_links as $link) {
                    echo '<li class="trpro-pagination-item">' . $link . '</li>';
                }
                echo '</ul>';
                echo '</nav>';
            }
            ?>
        </div>
    <?php endif; ?>
</div>

<!-- ✅ NOUVEAU : Modal pour afficher le profil détaillé -->
<div id="trpro-profile-modal" class="trpro-modal-overlay" style="display: none;">
    <div class="trpro-modal-container">
        <div class="trpro-modal-header">
            <h4 id="trpro-modal-title">Profil Formateur</h4>
            <button class="trpro-modal-close">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="trpro-modal-content" id="trpro-modal-content">
            <div class="trpro-modal-loading">
                <i class="fas fa-spinner fa-spin"></i>
                <p>Chargement du profil...</p>
            </div>
        </div>
    </div>
</div>

<style>
/* Styles pour la recherche moderne */
.trpro-search-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 32px 24px;
}

.trpro-search-header {
    text-align: center;
    margin-bottom: 32px;
}

.trpro-search-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 16px;
}

.trpro-search-title i {
    color: #3b82f6;
}

.trpro-search-subtitle {
    font-size: 1.125rem;
    color: #64748b;
    line-height: 1.6;
}

.trpro-search-form-modern {
    background: white;
    border-radius: 16px;
    padding: 32px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05), 0 10px 25px rgba(0, 0, 0, 0.1);
    margin-bottom: 32px;
}

.trpro-search-main {
    display: flex;
    gap: 16px;
    margin-bottom: 24px;
}

.trpro-search-input-group {
    flex: 1;
    position: relative;
}

.trpro-search-input {
    width: 100%;
    padding: 16px 50px 16px 20px;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    font-size: 16px;
    transition: all 0.3s ease;
}

.trpro-search-input:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.trpro-search-clear {
    position: absolute;
    right: 16px;
    top: 50%;
    transform: translateY(-50%);
    background: #ef4444;
    color: white;
    border: none;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
}

.trpro-search-btn {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: white;
    border: none;
    border-radius: 12px;
    padding: 16px 32px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 8px;
    white-space: nowrap;
}

.trpro-search-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(59, 130, 246, 0.3);
}

.trpro-filters-section {
    border-top: 1px solid #e2e8f0;
    padding-top: 24px;
}

.trpro-filters-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
}

.trpro-filter-group label {
    display: block;
    font-weight: 600;
    color: #374151;
    margin-bottom: 8px;
    font-size: 14px;
}

.trpro-filter-select {
    width: 100%;
    padding: 12px 16px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    background: white;
    font-size: 14px;
    transition: all 0.3s ease;
}

.trpro-filter-select:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.trpro-popular-tags {
    margin-top: 24px;
    padding-top: 24px;
    border-top: 1px solid #e2e8f0;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 12px;
}

.trpro-tags-label {
    font-weight: 600;
    color: #64748b;
    font-size: 14px;
}

.trpro-tag {
    background: #f1f5f9;
    color: #475569;
    border: 1px solid #e2e8f0;
    border-radius: 20px;
    padding: 6px 16px;
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
}

.trpro-tag:hover {
    background: #3b82f6;
    color: white;
    border-color: #3b82f6;
    transform: translateY(-1px);
}

.trpro-tag.active {
    background: #3b82f6;
    color: white;
    border-color: #3b82f6;
}

/* États de chargement */
.trpro-search-loading {
    text-align: center;
    padding: 48px 24px;
    color: #64748b;
}

.trpro-loading-spinner {
    font-size: 2rem;
    margin-bottom: 16px;
    color: #3b82f6;
}

.trpro-empty-state {
    text-align: center;
    padding: 48px 24px;
    color: #64748b;
}

.trpro-empty-icon {
    font-size: 3rem;
    color: #94a3b8;
    margin-bottom: 16px;
}

.trpro-empty-state h3 {
    color: #374151;
    margin-bottom: 8px;
}

/* Header des résultats */
.trpro-results-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    padding: 16px 0;
    border-bottom: 1px solid #e2e8f0;
}

.trpro-results-info h3 {
    color: #1e293b;
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 4px;
}

.trpro-results-info p {
    color: #64748b;
    font-size: 14px;
}

.trpro-view-switcher {
    display: flex;
    gap: 4px;
    background: #f1f5f9;
    padding: 4px;
    border-radius: 8px;
}

.trpro-view-btn {
    background: transparent;
    border: none;
    padding: 8px 12px;
    border-radius: 6px;
    color: #64748b;
    cursor: pointer;
    transition: all 0.3s ease;
}

.trpro-view-btn.active {
    background: white;
    color: #3b82f6;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

/* Grille des formateurs */
.trpro-trainers-grid {
    display: grid;
    gap: 24px;
}

.trpro-view-grid {
    grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));
}

.trpro-view-list {
    grid-template-columns: 1fr;
}

.trpro-trainer-card-modern {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1), 0 1px 2px rgba(0, 0, 0, 0.06);
    transition: all 0.3s ease;
    position: relative;
    border: 1px solid #e2e8f0;
}

.trpro-trainer-card-modern:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1), 0 4px 6px rgba(0, 0, 0, 0.05);
    border-color: #3b82f6;
}

.trpro-card-header {
    position: relative;
    padding: 24px 24px 16px;
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
}

.trpro-trainer-avatar {
    position: relative;
}

.trpro-trainer-avatar img,
.trpro-avatar-placeholder {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #e2e8f0;
}

.trpro-avatar-placeholder {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.75rem;
}

.trpro-status-badge {
    position: absolute;
    bottom: -8px;
    right: -8px;
    background: #10b981;
    color: white;
    border-radius: 12px;
    padding: 4px 8px;
    font-size: 11px;
    font-weight: 600;
    border: 2px solid white;
    display: flex;
    align-items: center;
    gap: 4px;
}

.trpro-verification-badges {
    display: flex;
    gap: 8px;
}

.trpro-badge {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
}

.trpro-verified {
    background: #10b981;
    color: white;
}

.trpro-cv-badge {
    background: #ef4444;
    color: white;
}

.trpro-card-body {
    padding: 0 24px 16px;
}

.trpro-trainer-identity {
    margin-bottom: 16px;
}

.trpro-trainer-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 4px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.trpro-trainer-id {
    background: #f1f5f9;
    color: #64748b;
    font-size: 12px;
    font-weight: 500;
    padding: 2px 8px;
    border-radius: 12px;
}

.trpro-trainer-company {
    display: flex;
    align-items: center;
    gap: 6px;
    color: #64748b;
    font-size: 14px;
}

.trpro-specialties-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 8px;
    margin-bottom: 16px;
}

.trpro-specialty-item {
    display: flex;
    align-items: center;
    gap: 6px;
    background: #f8fafc;
    padding: 6px 10px;
    border-radius: 8px;
    font-size: 12px;
    color: #475569;
    border: 1px solid #e2e8f0;
}

.trpro-specialty-item i {
    color: #3b82f6;
    font-size: 13px;
}

.trpro-specialty-more {
    background: #3b82f6;
    color: white;
    border-color: #3b82f6;
    font-weight: 600;
}

/* ✅ NOUVEAU : Styles pour les régions */
.trpro-regions-section {
    margin-bottom: 16px;
}

.trpro-regions-list {
    display: flex;
    align-items: center;
    gap: 8px;
    background: #ecfdf5;
    color: #059669;
    padding: 8px 12px;
    border-radius: 8px;
    font-size: 13px;
    border: 1px solid #d1fae5;
}

.trpro-regions-list i {
    color: #10b981;
}

.trpro-regions-more {
    background: #10b981;
    color: white;
    padding: 2px 6px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 11px;
}

.trpro-experience-preview {
    margin-bottom: 16px;
}

.trpro-experience-text {
    background: #f8fafc;
    color: #64748b;
    padding: 12px;
    border-radius: 8px;
    font-size: 13px;
    line-height: 1.5;
    border-left: 3px solid #3b82f6;
}

.trpro-trainer-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    margin-bottom: 16px;
}

.trpro-meta-item {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    color: #64748b;
    background: #f1f5f9;
    padding: 4px 8px;
    border-radius: 12px;
}

.trpro-meta-item i {
    color: #3b82f6;
    font-size: 13px;
}

.trpro-card-footer {
    padding: 0 24px 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.trpro-action-buttons {
    display: flex;
    gap: 8px;
}

.trpro-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
    border: 2px solid transparent;
    cursor: pointer;
}

.trpro-btn-primary {
    background: #3b82f6;
    color: white;
}

.trpro-btn-primary:hover {
    background: #2563eb;
    transform: translateY(-1px);
    text-decoration: none;
    color: white;
}

.trpro-btn-outline {
    background: transparent;
    color: #64748b;
    border-color: #d1d5db;
}

.trpro-btn-outline:hover {
    background: #f8fafc;
    border-color: #3b82f6;
    color: #3b82f6;
    text-decoration: none;
}

.trpro-social-link {
    color: #0077b5;
    font-size: 20px;
    transition: all 0.3s ease;
}

.trpro-social-link:hover {
    color: #005885;
    transform: scale(1.1);
}

.trpro-popularity-indicator {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: #f1f5f9;
    overflow: hidden;
}

.trpro-popularity-bar {
    height: 100%;
    background: linear-gradient(90deg, #10b981 0%, #3b82f6 100%);
    transition: width 0.3s ease;
}

/* ✅ Modal pour le profil détaillé */
.trpro-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 10000;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.trpro-modal-container {
    background: white;
    border-radius: 16px;
    max-width: 800px;
    width: 100%;
    max-height: 90vh;
    overflow: hidden;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.trpro-modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 24px;
    border-bottom: 1px solid #e2e8f0;
    background: #f8fafc;
}

.trpro-modal-header h4 {
    color: #1e293b;
    font-size: 1.25rem;
    font-weight: 600;
    margin: 0;
}

.trpro-modal-close {
    background: none;
    border: none;
    color: #64748b;
    font-size: 20px;
    cursor: pointer;
    padding: 8px;
    border-radius: 50%;
    transition: all 0.3s ease;
}

.trpro-modal-close:hover {
    background: #e2e8f0;
    color: #374151;
}

.trpro-modal-content {
    padding: 24px;
    max-height: 70vh;
    overflow-y: auto;
}

.trpro-modal-loading {
    text-align: center;
    padding: 48px;
    color: #64748b;
}

.trpro-modal-loading i {
    font-size: 2rem;
    color: #3b82f6;
    margin-bottom: 16px;
}

/* Responsive */
@media (max-width: 768px) {
    .trpro-search-form-modern {
        padding: 24px;
    }
    
    .trpro-search-main {
        flex-direction: column;
    }
    
    .trpro-filters-row {
        grid-template-columns: 1fr;
    }
    
    .trpro-popular-tags {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .trpro-trainers-grid {
        grid-template-columns: 1fr;
    }
    
    .trpro-results-header {
        flex-direction: column;
        gap: 16px;
        align-items: flex-start;
    }
    
    .trpro-action-buttons {
        flex-direction: column;
        width: 100%;
    }
    
    .trpro-btn {
        justify-content: center;
    }
}
</style>