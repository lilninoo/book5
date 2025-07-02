<?php
/**
 * Classe pour la partie publique du plugin - VERSION COMPLÈTE avec régions et anonymisation
 * 
 * Fichier: includes/class-trainer-registration-public.php
 */

if (!defined('ABSPATH')) {
    exit;
}

class TrainerRegistrationPublic {

    public function __construct() {
        // Enqueue conditionnel uniquement quand nécessaire
        add_action('wp_enqueue_scripts', array($this, 'conditional_enqueue'));
        
        // AJAX handlers
        add_action('wp_ajax_submit_trainer_registration', array($this, 'handle_trainer_registration'));
        add_action('wp_ajax_nopriv_submit_trainer_registration', array($this, 'handle_trainer_registration'));
        add_action('wp_ajax_search_trainers', array($this, 'handle_trainer_search'));
        add_action('wp_ajax_nopriv_search_trainers', array($this, 'handle_trainer_search'));
        add_action('wp_ajax_contact_trainer', array($this, 'handle_trainer_contact'));
        add_action('wp_ajax_nopriv_contact_trainer', array($this, 'handle_trainer_contact'));
        
        // ✅ NOUVEAU : Handler pour la recherche avancée avec régions
        add_action('wp_ajax_advanced_search_trainers', array($this, 'handle_advanced_trainer_search'));
        add_action('wp_ajax_nopriv_advanced_search_trainers', array($this, 'handle_advanced_trainer_search'));
        
        // ✅ NOUVEAU : Handler pour récupérer le profil détaillé
        add_action('wp_ajax_get_trainer_profile', array($this, 'handle_get_trainer_profile'));
        add_action('wp_ajax_nopriv_get_trainer_profile', array($this, 'handle_get_trainer_profile'));
        
        // Hooks pour améliorer l'intégration
        add_action('wp_head', array($this, 'add_custom_css_variables'));
        add_filter('body_class', array($this, 'add_body_classes'));
    }

    /**
     * Enqueue conditionnel
     */
    public function conditional_enqueue() {
        global $post;
        
        if (!$post) return;
        
        $content = $post->post_content;
        $has_trainer_shortcode = (
            has_shortcode($content, 'trainer_home') ||
            has_shortcode($content, 'trainer_registration_form') ||
            has_shortcode($content, 'trainer_list') ||
            has_shortcode($content, 'trainer_search') ||
            has_shortcode($content, 'trainer_profile') ||
            has_shortcode($content, 'trainer_stats') ||
            has_shortcode($content, 'trainer_contact_form')
        );
        
        if (!$has_trainer_shortcode) return;
        
        // Enqueue CSS principal
        wp_enqueue_style(
            'trpro-public-style',
            TRAINER_REGISTRATION_PLUGIN_URL . 'public/css/public-style.css',
            array(),
            TRAINER_REGISTRATION_VERSION,
            'all'
        );
        
        // FontAwesome
        wp_enqueue_style(
            'trpro-fontawesome',
            'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
            array(),
            '6.4.0'
        );
        
        // JavaScript principal
        wp_enqueue_script(
            'trpro-public-script',
            TRAINER_REGISTRATION_PLUGIN_URL . 'public/js/public-script.js',
            array('jquery'),
            TRAINER_REGISTRATION_VERSION,
            true
        );
        
        // Configuration AJAX avec nouvelles fonctionnalités
        wp_localize_script('trpro-public-script', 'trainer_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('trainer_registration_nonce'),
            'messages' => array(
                'success' => __('Inscription réussie ! Nous vous contacterons bientôt.', 'trainer-registration'),
                'error' => __('Erreur lors de l\'inscription. Veuillez réessayer.', 'trainer-registration'),
                'required' => __('Ce champ est obligatoire.', 'trainer-registration'),
                'loading' => __('Chargement en cours...', 'trainer-registration'),
                'search_no_results' => __('Aucun formateur trouvé pour cette recherche.', 'trainer-registration'),
                'contact_success' => __('Message envoyé avec succès !', 'trainer-registration'),
                'contact_error' => __('Erreur lors de l\'envoi du message.', 'trainer-registration')
            ),
            'settings' => array(
                'max_file_size' => 5 * 1024 * 1024, // 5MB
                'allowed_file_types' => array('pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'gif'),
                'search_delay' => 300,
                'animation_duration' => 300
            ),
            'regions' => array(
                'ile-de-france' => 'Île-de-France',
                'auvergne-rhone-alpes' => 'Auvergne-Rhône-Alpes',
                'nouvelle-aquitaine' => 'Nouvelle-Aquitaine',
                'occitanie' => 'Occitanie',
                'hauts-de-france' => 'Hauts-de-France',
                'grand-est' => 'Grand Est',
                'provence-alpes-cote-azur' => 'Provence-Alpes-Côte d\'Azur',
                'pays-de-la-loire' => 'Pays de la Loire',
                'bretagne' => 'Bretagne',
                'normandie' => 'Normandie',
                'bourgogne-franche-comte' => 'Bourgogne-Franche-Comté',
                'centre-val-de-loire' => 'Centre-Val de Loire',
                'corse' => 'Corse',
                'outre-mer' => 'Outre-mer (DOM-TOM)',
                'europe' => 'Europe (hors France)',
                'international' => 'International',
                'distanciel' => 'Formation à distance'
            )
        ));
    }
    
    /**
     * ✅ NOUVEAU : Gestion robuste de l'AJAX avec régions
     */
    public function handle_trainer_registration() {
        try {
            // Vérification du nonce
            if (!wp_verify_nonce($_POST['nonce'], 'trainer_registration_nonce')) {
                wp_send_json_error(array(
                    'message' => 'Erreur de sécurité. Veuillez recharger la page.',
                    'code' => 'invalid_nonce'
                ));
            }

            // Validation des données avec régions
            $validation_result = $this->validate_form_data_with_regions($_POST, $_FILES);
            if (!$validation_result['valid']) {
                wp_send_json_error(array(
                    'message' => 'Données invalides. Veuillez corriger les erreurs.',
                    'errors' => $validation_result['errors'],
                    'code' => 'validation_failed'
                ));
            }

            // Traitement des fichiers
            $files_result = $this->handle_file_uploads($_FILES);
            if (!$files_result['success']) {
                wp_send_json_error(array(
                    'message' => $files_result['message'],
                    'code' => 'file_upload_failed'
                ));
            }

            // Préparation des données avec régions
            $trainer_data = $this->prepare_trainer_data_with_regions($_POST, $files_result);
            
            // Vérification email unique
            if ($this->email_exists($trainer_data['email'])) {
                wp_send_json_error(array(
                    'message' => 'Cet email est déjà enregistré. Utilisez une autre adresse email.',
                    'code' => 'email_exists'
                ));
            }

            // Insertion en base
            $trainer_id = $this->insert_trainer($trainer_data);
            
            if (!$trainer_id) {
                wp_send_json_error(array(
                    'message' => 'Erreur lors de l\'enregistrement. Veuillez réessayer.',
                    'code' => 'database_error'
                ));
            }

            // Notifications
            $this->send_notifications($trainer_data, $trainer_id);

            // Succès
            $success_message = get_option('trainer_auto_approve', 0) 
                ? 'Votre inscription a été validée avec succès ! Vous recevrez bientôt des opportunités.' 
                : 'Votre inscription a été envoyée avec succès ! Nous examinerons votre profil et vous contacterons bientôt.';

            wp_send_json_success(array(
                'message' => $success_message,
                'trainer_id' => $trainer_id,
                'redirect' => home_url('/catalogue-formateurs/'),
                'status' => $trainer_data['status']
            ));

        } catch (Exception $e) {
            error_log('Trainer Registration Error: ' . $e->getMessage());
            wp_send_json_error(array(
                'message' => 'Une erreur inattendue s\'est produite. Veuillez réessayer.',
                'code' => 'unexpected_error'
            ));
        }
    }

    /**
     * ✅ NOUVEAU : Préparation des données avec régions
     */
    private function prepare_trainer_data_with_regions($post_data, $files_result) {
        // Traitement des régions d'intervention
        $intervention_regions = '';
        if (isset($post_data['intervention_regions']) && is_array($post_data['intervention_regions'])) {
            $intervention_regions = implode(', ', array_map('sanitize_text_field', $post_data['intervention_regions']));
        }

        return array(
            'first_name' => sanitize_text_field($post_data['first_name']),
            'last_name' => sanitize_text_field($post_data['last_name']),
            'email' => sanitize_email($post_data['email']),
            'phone' => sanitize_text_field($post_data['phone']),
            'company' => sanitize_text_field($post_data['company']),
            'linkedin_url' => esc_url_raw($post_data['linkedin_url'] ?? ''),
            'specialties' => implode(', ', array_map('sanitize_text_field', $post_data['specialties'])),
            'intervention_regions' => $intervention_regions, // ✅ NOUVEAU
            'availability' => sanitize_text_field($post_data['availability']),
            'hourly_rate' => sanitize_text_field($post_data['hourly_rate'] ?? ''),
            'experience' => sanitize_textarea_field($post_data['experience']),
            'bio' => sanitize_textarea_field($post_data['bio'] ?? ''),
            'cv_file' => $files_result['cv_file'],
            'photo_file' => $files_result['photo_file'],
            'rgpd_consent' => isset($post_data['rgpd_consent']) ? 1 : 0,
            'marketing_consent' => isset($post_data['marketing_consent']) ? 1 : 0,
            'status' => get_option('trainer_auto_approve', 0) ? 'approved' : 'pending',
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        );
    }

    /**
     * ✅ NOUVEAU : Validation avec régions
     */
    private function validate_form_data_with_regions($post_data, $files_data) {
        $errors = array();
        
        // Champs obligatoires
        $required_fields = array(
            'first_name' => 'Le prénom est obligatoire',
            'last_name' => 'Le nom est obligatoire', 
            'email' => 'L\'email est obligatoire',
            'phone' => 'Le téléphone est obligatoire',
            'experience' => 'L\'expérience est obligatoire',
            'availability' => 'La disponibilité est obligatoire'
        );

        foreach ($required_fields as $field => $message) {
            if (empty($post_data[$field])) {
                $errors[] = $message;
            }
        }

        // Validation email
        if (!empty($post_data['email']) && !is_email($post_data['email'])) {
            $errors[] = 'Format d\'email invalide';
        }

        // Validation spécialités
        if (empty($post_data['specialties']) || !is_array($post_data['specialties'])) {
            $errors[] = 'Veuillez sélectionner au moins une spécialité';
        }

        // ✅ NOUVEAU : Validation régions d'intervention obligatoires
        if (empty($post_data['intervention_regions']) || !is_array($post_data['intervention_regions'])) {
            $errors[] = 'Veuillez sélectionner au moins une zone d\'intervention';
        }

        // Validation expérience (minimum 50 caractères)
        if (!empty($post_data['experience']) && strlen($post_data['experience']) < 50) {
            $errors[] = 'L\'expérience doit contenir au moins 50 caractères';
        }

        // Validation consentement RGPD
        if (empty($post_data['rgpd_consent'])) {
            $errors[] = 'Le consentement RGPD est obligatoire';
        }

        // Validation CV obligatoire
        if (empty($files_data['cv_file']['name'])) {
            $errors[] = 'Le CV est obligatoire';
        }

        // Validation URL LinkedIn si fournie (optionnelle)
        if (!empty($post_data['linkedin_url']) && !filter_var($post_data['linkedin_url'], FILTER_VALIDATE_URL)) {
            $errors[] = 'L\'URL LinkedIn n\'est pas valide';
        }

        return array(
            'valid' => empty($errors),
            'errors' => $errors
        );
    }

    /**
     * ✅ NOUVEAU : Recherche avancée avec régions
     */
    public function handle_advanced_trainer_search() {
        if (!wp_verify_nonce($_POST['nonce'], 'trainer_registration_nonce')) {
            wp_send_json_error(array('message' => 'Token de sécurité invalide'));
        }
        
        try {
            $search_params = array(
                'search_term' => sanitize_text_field($_POST['search_term'] ?? ''),
                'specialty_filter' => sanitize_text_field($_POST['specialty_filter'] ?? ''),
                'region_filter' => sanitize_text_field($_POST['region_filter'] ?? ''),
                'multi_regions' => isset($_POST['multi_regions']) ? array_map('sanitize_text_field', $_POST['multi_regions']) : array(),
                'availability_filter' => sanitize_text_field($_POST['availability_filter'] ?? ''),
                'experience_filter' => sanitize_text_field($_POST['experience_filter'] ?? ''),
                'rate_filter' => sanitize_text_field($_POST['rate_filter'] ?? ''),
                'per_page' => min(50, max(1, intval($_POST['per_page'] ?? 12))),
                'page' => max(1, intval($_POST['page'] ?? 1))
            );
            
            $results = $this->perform_advanced_trainer_search($search_params);
            
            if ($results === false) {
                wp_send_json_error(array(
                    'message' => 'Erreur lors de la recherche',
                    'code' => 'search_error'
                ));
            }
            
            // ✅ Ajouter les noms anonymisés dans les résultats
            foreach ($results['trainers'] as $trainer) {
                $trainer->display_name = $this->get_anonymized_name($trainer->first_name, $trainer->last_name);
            }
            
            wp_send_json_success($results);
            
        } catch (Exception $e) {
            error_log('Advanced Search Error: ' . $e->getMessage());
            wp_send_json_error(array(
                'message' => 'Erreur interne du serveur',
                'code' => 'server_error'
            ));
        }
    }

    /**
     * ✅ NOUVEAU : Logique de recherche avancée avec régions
     */
    private function perform_advanced_trainer_search($params) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'trainer_registrations';
        
        // Construire les conditions WHERE
        $where_conditions = array("status = 'approved'");
        $sql_params = array();
        
        // Recherche textuelle
        if (!empty($params['search_term'])) {
            $where_conditions[] = '(first_name LIKE %s OR last_name LIKE %s OR specialties LIKE %s OR bio LIKE %s OR experience LIKE %s OR company LIKE %s)';
            $search_param = '%' . $wpdb->esc_like($params['search_term']) . '%';
            $sql_params = array_merge($sql_params, array($search_param, $search_param, $search_param, $search_param, $search_param, $search_param));
        }
        
        // Filtre par spécialité
        if (!empty($params['specialty_filter']) && $params['specialty_filter'] !== 'all') {
            $where_conditions[] = 'specialties LIKE %s';
            $sql_params[] = '%' . $wpdb->esc_like($params['specialty_filter']) . '%';
        }
        
        // ✅ NOUVEAU : Filtre par région simple
        if (!empty($params['region_filter']) && $params['region_filter'] !== 'all') {
            $where_conditions[] = 'intervention_regions LIKE %s';
            $sql_params[] = '%' . $wpdb->esc_like($params['region_filter']) . '%';
        }
        
        // ✅ NOUVEAU : Filtre par régions multiples
        if (!empty($params['multi_regions'])) {
            $region_conditions = array();
            foreach ($params['multi_regions'] as $region) {
                $region_conditions[] = 'intervention_regions LIKE %s';
                $sql_params[] = '%' . $wpdb->esc_like($region) . '%';
            }
            if (!empty($region_conditions)) {
                $where_conditions[] = '(' . implode(' OR ', $region_conditions) . ')';
            }
        }
        
        // Filtre par disponibilité
        if (!empty($params['availability_filter'])) {
            $where_conditions[] = 'availability = %s';
            $sql_params[] = $params['availability_filter'];
        }
        
        // Filtre par expérience (basé sur la longueur du texte d'expérience)
        if (!empty($params['experience_filter'])) {
            switch ($params['experience_filter']) {
                case 'junior':
                    $where_conditions[] = 'CHAR_LENGTH(experience) < 500';
                    break;
                case 'intermediaire':
                    $where_conditions[] = 'CHAR_LENGTH(experience) BETWEEN 500 AND 1000';
                    break;
                case 'senior':
                    $where_conditions[] = 'CHAR_LENGTH(experience) BETWEEN 1000 AND 2000';
                    break;
                case 'expert':
                    $where_conditions[] = 'CHAR_LENGTH(experience) > 2000';
                    break;
            }
        }
        
        // Filtre par tarif horaire
        if (!empty($params['rate_filter'])) {
            switch ($params['rate_filter']) {
                case '0-50':
                    $where_conditions[] = 'CAST(REGEXP_REPLACE(hourly_rate, "[^0-9]", "") AS UNSIGNED) < 50';
                    break;
                case '50-80':
                    $where_conditions[] = 'CAST(REGEXP_REPLACE(hourly_rate, "[^0-9]", "") AS UNSIGNED) BETWEEN 50 AND 80';
                    break;
                case '80-120':
                    $where_conditions[] = 'CAST(REGEXP_REPLACE(hourly_rate, "[^0-9]", "") AS UNSIGNED) BETWEEN 80 AND 120';
                    break;
                case '120+':
                    $where_conditions[] = 'CAST(REGEXP_REPLACE(hourly_rate, "[^0-9]", "") AS UNSIGNED) > 120';
                    break;
            }
        }
        
        $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
        
        // Calculer l'offset
        $offset = ($params['page'] - 1) * $params['per_page'];
        
        // Compter le total
        $count_query = "SELECT COUNT(*) FROM $table_name $where_clause";
        if (!empty($sql_params)) {
            $count_query = $wpdb->prepare($count_query, $sql_params);
        }
        
        $total = $wpdb->get_var($count_query);
        
        if ($total === null) {
            error_log('Database Error (count): ' . $wpdb->last_error);
            return false;
        }
        
        // Récupérer les formateurs avec tri intelligent
        $order_clause = "ORDER BY created_at DESC";
        
        // Tri intelligent basé sur la pertinence
        if (!empty($params['search_term'])) {
            $order_clause = "ORDER BY 
                CASE 
                    WHEN specialties LIKE '%" . $wpdb->esc_like($params['search_term']) . "%' THEN 1
                    WHEN experience LIKE '%" . $wpdb->esc_like($params['search_term']) . "%' THEN 2
                    WHEN bio LIKE '%" . $wpdb->esc_like($params['search_term']) . "%' THEN 3
                    ELSE 4
                END,
                created_at DESC";
        }
        
        $trainers_query = "SELECT * FROM $table_name $where_clause $order_clause LIMIT %d OFFSET %d";
        $final_params = array_merge($sql_params, array($params['per_page'], $offset));
        $trainers_query = $wpdb->prepare($trainers_query, $final_params);
        
        $trainers = $wpdb->get_results($trainers_query);
        
        if ($trainers === null) {
            error_log('Database Error (select): ' . $wpdb->last_error);
            return false;
        }
        
        // Traiter les données des formateurs
        $upload_dir = wp_upload_dir();
        foreach ($trainers as $trainer) {
            // Ajouter l'URL de la photo si elle existe
            if (!empty($trainer->photo_file)) {
                $trainer->photo_url = $upload_dir['baseurl'] . '/' . $trainer->photo_file;
            }
            
            // Ajouter l'URL du CV si il existe
            if (!empty($trainer->cv_file)) {
                $trainer->cv_url = $upload_dir['baseurl'] . '/' . $trainer->cv_file;
            }
            
            // ✅ NOUVEAU : Anonymiser le nom
            $trainer->display_name = $this->get_anonymized_name($trainer->first_name, $trainer->last_name);
            
            // Nettoyer les données sensibles côté client (garder nom pour admin)
            // Ne pas supprimer first_name et last_name car nécessaires pour l'anonymisation
        }
        
        return array(
            'trainers' => $trainers,
            'total' => intval($total),
            'page' => $params['page'],
            'per_page' => $params['per_page'],
            'total_pages' => ceil($total / $params['per_page']),
            'search_params' => $params
        );
    }

    /**
     * ✅ NOUVEAU : Générer le nom anonymisé
     */
    private function get_anonymized_name($first_name, $last_name) {
        if (empty($last_name) || empty($first_name)) {
            return 'Formateur Expert';
        }
        
        return strtoupper(substr($last_name, 0, 1)) . '. ' . $first_name;
    }

    /**
     * ✅ NOUVEAU : Handler pour récupérer le profil détaillé
     */
    public function handle_get_trainer_profile() {
        if (!wp_verify_nonce($_POST['nonce'], 'trainer_registration_nonce')) {
            wp_send_json_error(array('message' => 'Token de sécurité invalide'));
        }
        
        $trainer_id = intval($_POST['trainer_id']);
        
        if (!$trainer_id) {
            wp_send_json_error(array('message' => 'ID formateur manquant'));
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'trainer_registrations';
        
        $trainer = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE id = %d AND status = 'approved'",
            $trainer_id
        ));
        
        if (!$trainer) {
            wp_send_json_error(array('message' => 'Formateur non trouvé'));
        }
        
        // Préparer les données pour l'affichage
        $upload_dir = wp_upload_dir();
        
        $profile_data = array(
            'id' => $trainer->id,
            'display_name' => $this->get_anonymized_name($trainer->first_name, $trainer->last_name),
            'company' => $trainer->company,
            'specialties' => explode(', ', $trainer->specialties),
            'intervention_regions' => !empty($trainer->intervention_regions) ? explode(', ', $trainer->intervention_regions) : array(),
            'availability' => $trainer->availability,
            'hourly_rate' => $trainer->hourly_rate,
            'experience' => $trainer->experience,
            'bio' => $trainer->bio,
            'linkedin_url' => $trainer->linkedin_url,
            'created_at' => $trainer->created_at,
            'photo_url' => !empty($trainer->photo_file) ? $upload_dir['baseurl'] . '/' . $trainer->photo_file : '',
            'cv_available' => !empty($trainer->cv_file)
        );
        
        wp_send_json_success($profile_data);
    }

    /**
     * Recherche simple (héritée)
     */
    public function handle_trainer_search() {
        if (!wp_verify_nonce($_POST['nonce'], 'trainer_registration_nonce')) {
            wp_send_json_error(array('message' => 'Security check failed'));
        }
        
        global $wpdb;
        $search_term = sanitize_text_field($_POST['search_term']);
        $specialty_filter = sanitize_text_field($_POST['specialty_filter']);
        $table_name = $wpdb->prefix . 'trainer_registrations';
        
        $where_clause = "WHERE status = 'approved'";
        $params = array();
        
        if (!empty($search_term)) {
            $where_clause .= " AND (specialties LIKE %s OR bio LIKE %s OR experience LIKE %s OR company LIKE %s)";
            $search_param = '%' . $search_term . '%';
            $params[] = $search_param;
            $params[] = $search_param;
            $params[] = $search_param;
            $params[] = $search_param;
        }
        
        if (!empty($specialty_filter) && $specialty_filter !== 'all') {
            $where_clause .= " AND specialties LIKE %s";
            $params[] = '%' . $specialty_filter . '%';
        }
        
        $query = "SELECT * FROM $table_name $where_clause ORDER BY created_at DESC LIMIT 20";
        
        if (!empty($params)) {
            $query = $wpdb->prepare($query, $params);
        }
        
        $trainers = $wpdb->get_results($query);
        
        // Anonymiser les noms dans les résultats
        foreach ($trainers as $trainer) {
            $trainer->display_name = $this->get_anonymized_name($trainer->first_name, $trainer->last_name);
        }
        
        ob_start();
        if (!empty($trainers)) {
            echo '<div class="trpro-trainers-grid">';
            foreach ($trainers as $trainer) {
                $template_path = TRAINER_REGISTRATION_PLUGIN_PATH . 'public/partials/trainer-card-modern.php';
                if (file_exists($template_path)) {
                    include $template_path;
                }
            }
            echo '</div>';
        }
        $html = ob_get_clean();
        
        wp_send_json_success(array(
            'html' => $html,
            'count' => count($trainers),
            'search_term' => $search_term,
            'specialty_filter' => $specialty_filter
        ));
    }

    // ===== MÉTHODES HÉRITÉES (inchangées) =====
    
    private function email_exists($email) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'trainer_registrations';
        
        return $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM $table_name WHERE email = %s",
            $email
        ));
    }

    private function insert_trainer($trainer_data) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'trainer_registrations';
        
        $inserted = $wpdb->insert($table_name, $trainer_data);
        
        if ($inserted === false) {
            error_log('Database error: ' . $wpdb->last_error);
            return false;
        }
        
        return $wpdb->insert_id;
    }

    /**
     * Gestion robuste des uploads
     */
    private function handle_file_uploads($files) {
        $uploaded_files = array(
            'cv_file' => '',
            'photo_file' => ''
        );

        $upload_dir = wp_upload_dir();
        $trainer_dir = $upload_dir['basedir'] . '/trainer-files/';
        
        // Créer le dossier si nécessaire
        if (!file_exists($trainer_dir)) {
            wp_mkdir_p($trainer_dir);
            // Sécuriser le dossier
            file_put_contents($trainer_dir . '.htaccess', "Options -Indexes\ndeny from all\n");
            file_put_contents($trainer_dir . 'index.php', '<?php // Silence is golden');
        }

        // Traitement du CV (obligatoire)
        if (!empty($files['cv_file']['name'])) {
            $cv_result = $this->upload_file($files['cv_file'], $trainer_dir . 'cv/', array(
                'pdf' => 'application/pdf',
                'doc' => 'application/msword',
                'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
            ), 5 * 1024 * 1024);

            if (!$cv_result['success']) {
                return array(
                    'success' => false,
                    'message' => 'Erreur CV: ' . $cv_result['message']
                );
            }
            
            $uploaded_files['cv_file'] = 'trainer-files/cv/' . $cv_result['filename'];
        }

        // Traitement de la photo (optionnel)
        if (!empty($files['photo_file']['name'])) {
            $photo_result = $this->upload_file($files['photo_file'], $trainer_dir . 'photos/', array(
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif'
            ), 2 * 1024 * 1024);

            if (!$photo_result['success']) {
                return array(
                    'success' => false,
                    'message' => 'Erreur Photo: ' . $photo_result['message']
                );
            }
            
            $uploaded_files['photo_file'] = 'trainer-files/photos/' . $photo_result['filename'];
        }

        return array(
            'success' => true,
            'cv_file' => $uploaded_files['cv_file'],
            'photo_file' => $uploaded_files['photo_file']
        );
    }

    /**
     * Upload sécurisé d'un fichier
     */
    private function upload_file($file, $target_dir, $allowed_types, $max_size) {
        // Vérifications de sécurité
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return array(
                'success' => false,
                'message' => 'Erreur lors de l\'upload: ' . $this->get_upload_error_message($file['error'])
            );
        }

        if ($file['size'] > $max_size) {
            return array(
                'success' => false,
                'message' => 'Fichier trop volumineux (max: ' . $this->format_bytes($max_size) . ')'
            );
        }

        // Vérification du type MIME
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime_type, $allowed_types)) {
            return array(
                'success' => false,
                'message' => 'Type de fichier non autorisé'
            );
        }

        // Créer le nom de fichier sécurisé
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = wp_unique_filename($target_dir, time() . '_' . sanitize_file_name(basename($file['name'], '.' . $extension)) . '.' . $extension);

        if (!file_exists($target_dir)) {
            wp_mkdir_p($target_dir);
        }

        $target_file = $target_dir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $target_file)) {
            return array(
                'success' => false,
                'message' => 'Impossible de sauvegarder le fichier'
            );
        }

        chmod($target_file, 0644);

        return array(
            'success' => true,
            'filename' => $filename,
            'path' => $target_file
        );
    }

    /**
     * Utilitaires
     */
    private function get_upload_error_message($error_code) {
        switch ($error_code) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return 'Fichier trop volumineux';
            case UPLOAD_ERR_PARTIAL:
                return 'Upload incomplet';
            case UPLOAD_ERR_NO_FILE:
                return 'Aucun fichier sélectionné';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Dossier temporaire manquant';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Impossible d\'écrire le fichier';
            case UPLOAD_ERR_EXTENSION:
                return 'Extension non autorisée';
            default:
                return 'Erreur inconnue';
        }
    }

    private function format_bytes($size, $precision = 2) {
        $units = array('B', 'KB', 'MB', 'GB');
        
        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        
        return round($size, $precision) . ' ' . $units[$i];
    }

    /**
     * Envoi des notifications
     */
    private function send_notifications($trainer_data, $trainer_id) {
        // Notification à l'admin
        if (get_option('trainer_notify_new_registration', 1)) {
            $admin_email = get_option('trainer_notification_email', get_option('admin_email'));
            $subject = 'Nouvelle inscription formateur - ' . $trainer_data['first_name'] . ' ' . $trainer_data['last_name'];
            
            $message = "Nouvelle inscription de formateur:\n\n";
            $message .= "Nom: " . $trainer_data['first_name'] . ' ' . $trainer_data['last_name'] . "\n";
            $message .= "Email: " . $trainer_data['email'] . "\n";
            $message .= "Téléphone: " . $trainer_data['phone'] . "\n";
            $message .= "Entreprise: " . $trainer_data['company'] . "\n";
            $message .= "Spécialités: " . $trainer_data['specialties'] . "\n";
            $message .= "Zones d'intervention: " . $trainer_data['intervention_regions'] . "\n"; // ✅ NOUVEAU
            $message .= "Statut: " . $trainer_data['status'] . "\n\n";
            $message .= "Voir dans l'admin: " . admin_url('admin.php?page=trainer-registration');

            wp_mail($admin_email, $subject, $message);
        }

        // Notification au formateur
        $trainer_email = $trainer_data['email'];
        $subject = 'Confirmation d\'inscription - ' . get_bloginfo('name');
        
        $message = "Bonjour " . $trainer_data['first_name'] . ",\n\n";
        
        if ($trainer_data['status'] === 'approved') {
            $message .= "Votre inscription en tant que formateur a été validée avec succès !\n\n";
            $message .= "Votre profil est maintenant visible dans notre catalogue et vous pourrez bientôt recevoir des opportunités de formations.\n\n";
        } else {
            $message .= "Nous avons bien reçu votre inscription en tant que formateur.\n\n";
            $message .= "Notre équipe va examiner votre profil et vous contactera bientôt.\n\n";
        }
        
        $message .= "Merci de votre confiance !\n\n";
        $message .= "L'équipe " . get_bloginfo('name');

        wp_mail($trainer_email, $subject, $message);
    }

    // ===== MÉTHODES HÉRITÉES POUR COMPATIBILITÉ =====
    
    public function add_custom_css_variables() {
        $primary_color = get_option('trpro_primary_color', '#000000');
        $secondary_color = get_option('trpro_secondary_color', '#6b7280');
        $accent_color = get_option('trpro_accent_color', '#fbbf24');
        
        echo "<style>
        :root {
            --trpro-primary-custom: {$primary_color};
            --trpro-secondary-custom: {$secondary_color};
            --trpro-accent-custom: {$accent_color};
        }
        </style>";
    }
    
    public function add_body_classes($classes) {
        global $post;
        
        if ($post) {
            if (has_shortcode($post->post_content, 'trainer_home')) {
                $classes[] = 'trpro-page-home';
            }
            if (has_shortcode($post->post_content, 'trainer_registration_form')) {
                $classes[] = 'trpro-page-registration';
            }
            if (has_shortcode($post->post_content, 'trainer_list')) {
                $classes[] = 'trpro-page-list';
            }
            if (has_shortcode($post->post_content, 'trainer_search')) {
                $classes[] = 'trpro-page-search';
            }
        }
        
        return $classes;
    }

    public function handle_trainer_contact() {
        if (!wp_verify_nonce($_POST['contact_nonce'], 'trainer_contact_nonce')) {
            wp_send_json_error(array('message' => 'Security check failed'));
        }
        
        $errors = array();
        
        if (empty($_POST['contact_name'])) $errors[] = 'Le nom est obligatoire';
        if (empty($_POST['contact_email']) || !is_email($_POST['contact_email'])) $errors[] = 'Email valide requis';
        if (empty($_POST['contact_message'])) $errors[] = 'Le message est obligatoire';
        if (empty($_POST['trainer_id'])) $errors[] = 'ID formateur manquant';
        
        if (!empty($errors)) {
            wp_send_json_error(array('errors' => $errors));
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'trainer_registrations';
        $trainer = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE id = %d AND status = 'approved'",
            intval($_POST['trainer_id'])
        ));
        
        if (!$trainer) {
            wp_send_json_error(array('message' => 'Formateur non trouvé'));
        }
        
        $contact_email = get_option('trainer_contact_email', get_option('admin_email'));
        $subject = 'Demande de contact pour le formateur #' . str_pad($trainer->id, 4, '0', STR_PAD_LEFT);
        
        $message = "Nouvelle demande de contact :\n\n";
        $message .= "Formateur concerné : #" . str_pad($trainer->id, 4, '0', STR_PAD_LEFT) . "\n";
        $message .= "Nom anonymisé : " . $this->get_anonymized_name($trainer->first_name, $trainer->last_name) . "\n";
        $message .= "Spécialités : " . $trainer->specialties . "\n";
        if (!empty($trainer->intervention_regions)) {
            $message .= "Zones d'intervention : " . $trainer->intervention_regions . "\n";
        }
        $message .= "\n--- Demandeur ---\n";
        $message .= "Nom : " . sanitize_text_field($_POST['contact_name']) . "\n";
        $message .= "Email : " . sanitize_email($_POST['contact_email']) . "\n";
        $message .= "Entreprise : " . sanitize_text_field($_POST['contact_company']) . "\n\n";
        $message .= "--- Message ---\n";
        $message .= sanitize_textarea_field($_POST['contact_message']) . "\n\n";
        $message .= "Répondez directement à cet email pour mettre en relation.";
        
        $headers = array('Reply-To: ' . sanitize_email($_POST['contact_email']));
        
        if (wp_mail($contact_email, $subject, $message, $headers)) {
            wp_send_json_success(array('message' => 'Votre message a été envoyé avec succès !'));
        } else {
            wp_send_json_error(array('message' => 'Erreur lors de l\'envoi du message'));
        }
    }
}