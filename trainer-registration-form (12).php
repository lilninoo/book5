<?php
/**
 * Template formulaire d'inscription CORRIGÉ - SANS CSS/JS INLINE
 * 
 * Fichier: public/partials/trainer-registration-form.php
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="trpro-registration-container">
    <div class="trpro-registration-header">
        <h1 class="trpro-form-title">Inscription Formateur IT</h1>
        <p class="trpro-form-subtitle">Rejoignez notre réseau d'experts et partagez votre expertise</p>
    </div>

    <!-- Progress Bar -->
    <div class="trpro-registration-progress">
        <div class="trpro-progress-step active" data-step="1">
            <span>1</span>
            <small>Informations personnelles</small>
        </div>
        <div class="trpro-progress-step" data-step="2">
            <span>2</span>
            <small>Expérience professionnelle</small>
        </div>
        <div class="trpro-progress-step" data-step="3">
            <span>3</span>
            <small>Documents</small>
        </div>
        <div class="trpro-progress-step" data-step="4">
            <span>4</span>
            <small>Validation</small>
        </div>
    </div>

    <!-- Messages -->
    <div id="trpro-form-messages" class="trpro-form-messages" style="display: none;"></div>

    <form id="trpro-trainer-registration-form" method="post" enctype="multipart/form-data" class="trpro-registration-form">
        
        <!-- ÉTAPE 1: Informations personnelles -->
        <div class="trpro-form-step active" data-step="1">
            <h2 class="trpro-step-title">Informations Personnelles</h2>
            
            <div class="trpro-form-row">
                <div class="trpro-form-group">
                    <label for="trpro-first-name">Prénom *</label>
                    <input type="text" id="trpro-first-name" name="first_name" required>
                    <span class="trpro-error-message"></span>
                </div>
                
                <div class="trpro-form-group">
                    <label for="trpro-last-name">Nom *</label>
                    <input type="text" id="trpro-last-name" name="last_name" required>
                    <span class="trpro-error-message"></span>
                </div>
            </div>
            
            <div class="trpro-form-row">
                <div class="trpro-form-group">
                    <label for="trpro-email">Email professionnel *</label>
                    <input type="email" id="trpro-email" name="email" required>
                    <span class="trpro-error-message"></span>
                </div>
                
                <div class="trpro-form-group">
                    <label for="trpro-phone">Téléphone *</label>
                    <input type="tel" id="trpro-phone" name="phone" required>
                    <span class="trpro-error-message"></span>
                </div>
            </div>
            
            <div class="trpro-form-group">
                <label for="trpro-company">Entreprise / Organisation</label>
                <input type="text" id="trpro-company" name="company" placeholder="Nom de votre entreprise ou freelance">
            </div>
            
            <div class="trpro-form-group">
                <label for="trpro-linkedin-url">Profil LinkedIn</label>
                <input type="url" id="trpro-linkedin-url" name="linkedin_url" placeholder="https://linkedin.com/in/votre-profil">
            </div>
        </div>

        <!-- ÉTAPE 2: Expérience professionnelle -->
        <div class="trpro-form-step" data-step="2">
            <h2 class="trpro-step-title">Expertise & Expérience</h2>
            
            <div class="trpro-form-group">
                <label>Spécialités * (sélectionnez toutes qui s'appliquent)</label>
                <div class="trpro-checkbox-grid">
                    <!-- ✅ STRUCTURE CORRIGÉE -->
                    <div class="trpro-checkbox-item">
                        <input type="checkbox" name="specialties[]" value="administration-systeme" id="spec-admin">
                        <span class="trpro-checkmark"></span>
                        <label for="spec-admin">Administration Système</label>
                    </div>
                    <div class="trpro-checkbox-item">
                        <input type="checkbox" name="specialties[]" value="reseaux" id="spec-reseaux">
                        <span class="trpro-checkmark"></span>
                        <label for="spec-reseaux">Réseaux & Infrastructure</label>
                    </div>
                    <div class="trpro-checkbox-item">
                        <input type="checkbox" name="specialties[]" value="cloud" id="spec-cloud">
                        <span class="trpro-checkmark"></span>
                        <label for="spec-cloud">Cloud Computing</label>
                    </div>
                    <div class="trpro-checkbox-item">
                        <input type="checkbox" name="specialties[]" value="devops" id="spec-devops">
                        <span class="trpro-checkmark"></span>
                        <label for="spec-devops">DevOps & CI/CD</label>
                    </div>
                    <div class="trpro-checkbox-item">
                        <input type="checkbox" name="specialties[]" value="securite" id="spec-securite">
                        <span class="trpro-checkmark"></span>
                        <label for="spec-securite">Sécurité Informatique</label>
                    </div>
                    <div class="trpro-checkbox-item">
                        <input type="checkbox" name="specialties[]" value="telecoms" id="spec-telecoms">
                        <span class="trpro-checkmark"></span>
                        <label for="spec-telecoms">Télécommunications</label>
                    </div>
                    <div class="trpro-checkbox-item">
                        <input type="checkbox" name="specialties[]" value="developpement" id="spec-dev">
                        <span class="trpro-checkmark"></span>
                        <label for="spec-dev">Développement</label>
                    </div>
                    <div class="trpro-checkbox-item">
                        <input type="checkbox" name="specialties[]" value="bases-donnees" id="spec-db">
                        <span class="trpro-checkmark"></span>
                        <label for="spec-db">Bases de Données</label>
                    </div>
                </div>
                <span class="trpro-error-message" id="trpro-specialties-error"></span>
            </div>
            
            <div class="trpro-form-row">
                <div class="trpro-form-group">
                    <label for="trpro-availability">Disponibilité</label>
                    <select id="trpro-availability" name="availability">
                        <option value="">Sélectionnez votre disponibilité</option>
                        <option value="temps-plein">Temps plein</option>
                        <option value="temps-partiel">Temps partiel</option>
                        <option value="ponctuel">Missions ponctuelles</option>
                        <option value="weekends">Weekends uniquement</option>
                        <option value="flexible">Flexible</option>
                    </select>
                </div>
                
                <div class="trpro-form-group">
                    <label for="trpro-hourly-rate">Tarif horaire (optionnel)</label>
                    <input type="text" id="trpro-hourly-rate" name="hourly_rate" placeholder="Ex: 80€/h">
                </div>
            </div>
            
            <div class="trpro-form-group">
                <label for="trpro-experience">Expérience et compétences techniques *</label>
                <textarea id="trpro-experience" name="experience" rows="6" required 
                          placeholder="Décrivez votre expérience, vos certifications, les technologies que vous maîtrisez..."></textarea>
                <span class="trpro-error-message"></span>
            </div>
            
            <div class="trpro-form-group">
                <label for="trpro-bio">Présentation professionnelle</label>
                <textarea id="trpro-bio" name="bio" rows="4" 
                          placeholder="Présentez-vous en quelques mots, votre approche pédagogique..."></textarea>
            </div>
        </div>

        <!-- ÉTAPE 3: Documents -->
        <div class="trpro-form-step" data-step="3">
            <h2 class="trpro-step-title">Documents & Pièces Jointes</h2>
            
            <div class="trpro-upload-section">
                <div class="trpro-form-group">
                    <label for="trpro-cv-file">CV / Portfolio * (PDF, DOC, DOCX - Max 5MB)</label>
                    <div class="trpro-file-upload-area" data-target="trpro-cv-file">
                        <div class="trpro-upload-text">
                            <div class="trpro-upload-icon">
                                <i class="fas fa-cloud-upload-alt"></i>
                            </div>
                            <p>Glissez votre CV ici ou <span class="trpro-upload-link">cliquez pour sélectionner</span></p>
                            <small>Formats acceptés: PDF, DOC, DOCX</small>
                        </div>
                    </div>
                    <!-- ✅ ID CORRIGÉ pour correspondre au data-target -->
                    <input type="file" id="trpro-cv-file" name="cv_file" accept=".pdf,.doc,.docx" required style="display: none;">
                    <div class="trpro-file-preview" id="trpro-cv-file-preview"></div>
                    <span class="trpro-error-message"></span>
                </div>
                
                <div class="trpro-form-group">
                    <label for="trpro-photo-file">Photo professionnelle (optionnel - JPG, PNG - Max 2MB)</label>
                    <div class="trpro-file-upload-area" data-target="trpro-photo-file">
                        <div class="trpro-upload-text">
                            <div class="trpro-upload-icon">
                                <i class="fas fa-camera"></i>
                            </div>
                            <p>Glissez votre photo ici ou <span class="trpro-upload-link">cliquez pour sélectionner</span></p>
                            <small>Formats acceptés: JPG, PNG, GIF</small>
                        </div>
                    </div>
                    <!-- ✅ ID CORRIGÉ -->
                    <input type="file" id="trpro-photo-file" name="photo_file" accept=".jpg,.jpeg,.png,.gif" style="display: none;">
                    <div class="trpro-file-preview" id="trpro-photo-file-preview"></div>
                </div>
            </div>
        </div>

        <!-- ÉTAPE 4: Validation et RGPD -->
        <div class="trpro-form-step" data-step="4">
            <h2 class="trpro-step-title">Validation & Consentement</h2>
            
            <div class="trpro-summary-section">
                <h3>Récapitulatif de votre inscription</h3>
                <div id="trpro-registration-summary" class="trpro-summary-content">
                    <!-- Le résumé sera généré automatiquement -->
                </div>
            </div>
            
            <div class="trpro-rgpd-section">
                <h3>Protection des données personnelles</h3>
                
                <div class="trpro-rgpd-info">
                    <div class="trpro-info-grid">
                        <div class="trpro-info-item">
                            <strong>Responsable du traitement :</strong>
                            <span><?php echo get_option('trainer_company_name', get_bloginfo('name')); ?></span>
                        </div>
                        <div class="trpro-info-item">
                            <strong>Finalité :</strong>
                            <span>Gestion des inscriptions de formateurs et mise en relation avec des recruteurs</span>
                        </div>
                        <div class="trpro-info-item">
                            <strong>Base légale :</strong>
                            <span>Consentement (Art. 6.1.a RGPD)</span>
                        </div>
                        <div class="trpro-info-item">
                            <strong>Durée de conservation :</strong>
                            <span><?php echo get_option('trainer_data_retention', 3); ?> ans à compter de votre dernière activité</span>
                        </div>
                    </div>
                </div>
                
                <!-- ✅ CONSENTEMENT RGPD CORRIGÉ -->
                <div class="trpro-consent-checkboxes">
                    <!-- CONSENTEMENT RGPD OBLIGATOIRE -->
                    <div class="trpro-consent-item trpro-required-consent">
                        <div class="trpro-consent-wrapper">
                            <div class="trpro-consent-checkbox">
                                <input type="checkbox" name="rgpd_consent" value="1" required id="trpro-rgpd-consent">
                                <span class="trpro-checkmark"></span>
                            </div>
                            <div class="trpro-consent-text">
                                <label for="trpro-rgpd-consent">
                                    <strong>J'accepte le traitement de mes données personnelles *</strong>
                                </label>
                                <p>Je consens au traitement de mes données personnelles pour la gestion de mon profil de formateur et la mise en relation avec des recruteurs potentiels.</p>
                            </div>
                        </div>
                        <!-- ✅ ÉLÉMENT MANQUANT AJOUTÉ -->
                        <span class="trpro-error-message" id="trpro-rgpd-error"></span>
                    </div>
                    
                    <!-- CONSENTEMENT MARKETING OPTIONNEL -->
                    <div class="trpro-consent-item trpro-optional-consent">
                        <div class="trpro-consent-wrapper">
                            <div class="trpro-consent-checkbox">
                                <input type="checkbox" name="marketing_consent" value="1" id="trpro-marketing-consent">
                                <span class="trpro-checkmark"></span>
                            </div>
                            <div class="trpro-consent-text">
                                <label for="trpro-marketing-consent">
                                    <strong>Communications marketing (optionnel)</strong>
                                </label>
                                <p>J'accepte de recevoir des informations sur de nouvelles opportunités et actualités de la plateforme.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="trpro-rights-info">
                    <div class="trpro-info-box">
                        <i class="fas fa-info-circle"></i>
                        <div>
                            <strong>Vos droits :</strong>
                            <p>Vous disposez d'un droit d'accès, de rectification, d'effacement, de portabilité, de limitation du traitement et d'opposition. 
                            Pour exercer vos droits : <a href="mailto:<?php echo get_option('trainer_contact_email', 'dpo@votre-site.com'); ?>"><?php echo get_option('trainer_contact_email', 'dpo@votre-site.com'); ?></a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation du formulaire -->
        <div class="trpro-form-navigation">
            <button type="button" id="trpro-prev-step" class="trpro-btn trpro-btn-secondary" style="display: none;">
                <i class="fas fa-arrow-left"></i>
                Précédent
            </button>
            
            <button type="button" id="trpro-next-step" class="trpro-btn trpro-btn-primary">
                Suivant
                <i class="fas fa-arrow-right"></i>
            </button>
            
            <button type="submit" id="trpro-submit-form" class="trpro-btn trpro-btn-success" style="display: none;">
                <i class="fas fa-paper-plane"></i>
                Envoyer ma candidature
            </button>
        </div>

        <?php wp_nonce_field('trainer_registration_nonce', 'nonce'); ?>
    </form>
    
    <!-- Loading overlay -->
    <div id="trpro-form-loading" class="trpro-loading-overlay" style="display: none;">
        <div class="trpro-loading-content">
            <div class="trpro-spinner"></div>
            <p>Envoi de votre candidature en cours...</p>
        </div>
    </div>
</div>