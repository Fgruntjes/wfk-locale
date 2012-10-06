<?php
return array(
    'wfklocale' => array(
        'enabled' => array(
            'en_GB' => 'en',
            'nl_NL' => 'nl',
        ),
        'default_locale' => 'en_GB',
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'wfknews' => __DIR__ . '/../view',
        ),
    ),
    'translator' => array(
        'translation_file_patterns' => array(
            array(
                'type'     => 'PhpArray',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.php',
                'text_domain' => 'wfklocale'
            ),
        ),
    ),
);
