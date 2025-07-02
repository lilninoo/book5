<?php
/**
 * Plugin Name: Trainer Registration Pro
 * Plugin URI: https://yoursite.com/trainer-registration-pro
 * Description: Plugin pour gérer les inscriptions des formateurs IT avec conformité RGPD
 * Version: 1.0.0
 * Author: Votre Nom
 * License: GPL v2 or later
 * Text Domain: trainer-registration-pro
 */

// Sécurité - Empêcher l'accès direct
if (!defined('ABSPATH')) {
    exit;
}

// Constantes du plugin
define('TRAINER_REGISTRATION_VERSION', '1.0.0');
define('TRAINER_REGISTRATION_PLUGIN_URL', plugin_dir_url(__FILE__));
define('TRAINER_REGISTRATION_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('TRAINER_REGISTRATION_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Classe principale du plugin
 */
class TrainerRegistrationPlugin {

    private static $instance = null;
    private $admin = null;
    private $public = null;
    private $shortcodes = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // Hooks d'activation/désactivation
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));

        // Initialisation du plugin
        add_action('init', array($this, 'init'));
        add_action('plugins_loaded', array($this, 'load_textdomain'));
    }

    public function init() {
        // Charger les dépendances
        $this->load_dependencies();
        
        // Initialiser les classes
        if (class_exists('TrainerRegistrationPublic')) {
            $this->public = new TrainerRegistrationPublic();
        }
        
        if (class_exists('TrainerRegistrationShortcodes')) {
            $this->shortcodes = new TrainerRegistrationShortcodes();
        }
        
        if (is_admin() && class_exists('TrainerRegistrationAdmin')) {
            $this->admin = new TrainerRegistrationAdmin();
        }
        
        // Hooks additionnels
        add_filter('upload_mimes', array($this, 'add_upload_mimes'));
    }

    public function load_textdomain() {
        load_plugin_textdomain(
            'trainer-registration-pro',
            false,
            dirname(plugin_basename(__FILE__)) . '/languages/'
        );
    }

    private function load_dependencies() {
        // Classes principales
        $required_files = array(
            'includes/class-trainer-registration-admin.php',
            'includes/class-trainer-registration-public.php',
            'includes/class-trainer-registration-shortcodes.php',
            'includes/functions.php'
        );
        
        foreach ($required_files as $file) {
            $file_path = TRAINER_REGISTRATION_PLUGIN_PATH . $file;
            if (file_exists($file_path)) {
                require_once $file_path;
            }
        }
    }

    public function activate() {
        // Créer les tables
        $this->create_tables();
        
        // Créer les dossiers d'upload
        $this->create_upload_folders();
        
        // Définir les options par défaut
        $this->set_default_options();
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }

    public function deactivate() {
        flush_rewrite_rules();
    }

    private function create_tables() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        // Table principale des inscriptions
        $table_name = $wpdb->prefix . 'trainer_registrations';
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            first_name varchar(100) NOT NULL,
            last_name varchar(100) NOT NULL,
            email varchar(100) NOT NULL,
            phone varchar(20) NOT NULL,
            company varchar(200),
            specialties text NOT NULL,
            experience text NOT NULL,
            cv_file varchar(255) NOT NULL,
            photo_file varchar(255),
            linkedin_url varchar(255),
            bio text,
            availability varchar(50),
            hourly_rate varchar(20),
            rgpd_consent tinyint(1) NOT NULL DEFAULT 0,
            marketing_consent tinyint(1) NOT NULL DEFAULT 0,
            status varchar(20) DEFAULT 'pending',
            admin_notes text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY email (email),
            KEY status (status),
            KEY created_at (created_at)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    private function create_upload_folders() {
        $upload_dir = wp_upload_dir();
        $folders = array(
            '/trainer-files/',
            '/trainer-files/cv/',
            '/trainer-files/photos/'
        );
        
        foreach ($folders as $folder) {
            $dir_path = $upload_dir['basedir'] . $folder;
            
            if (!file_exists($dir_path)) {
                wp_mkdir_p($dir_path);
                
                // Créer un fichier .htaccess pour la sécurité
                $htaccess_content = "Options -Indexes\ndeny from all\n";
                file_put_contents($dir_path . '.htaccess', $htaccess_content);
                
                // Créer un fichier index.php vide
                file_put_contents($dir_path . 'index.php', '<?php // Silence is golden');
            }
        }
    }

    private function set_default_options() {
        $defaults = array(
            'trainer_auto_approve' => 0,
            'trainer_require_photo' => 0,
            'trainer_max_cv_size' => 5, // MB
            'trainer_max_photo_size' => 2, // MB
            'trainer_notification_email' => get_option('admin_email'),
            'trainer_notify_new_registration' => 1,
            'trainer_contact_email' => get_option('admin_email'),
            'trainer_contact_phone' => '',
            'trainer_company_name' => get_bloginfo('name')
        );

        foreach ($defaults as $option => $value) {
            if (get_option($option) === false) {
                add_option($option, $value);
            }
        }
    }

    public function add_upload_mimes($mimes) {
        // Ajouter les types MIME pour les CV
        $mimes['doc'] = 'application/msword';
        $mimes['docx'] = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
        
        return $mimes;
    }
}

// Initialiser le plugin
TrainerRegistrationPlugin::get_instance();