<?php

namespace Elementor;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Widget_Push_Products extends Widget_Base
{

    public function get_name()
    {

        return 'push-products';
    }

    public function get_title()
    {
        return __('Push Products', 'elementor-custom');
    }

    public function get_categories()
    {
        return ['custom'];
    }

    public function get_icon()
    {
        return 'eicon-post-list';
    }

    protected function _register_controls()
    {
        $this->start_controls_section('titre', [
            'label' => __('Titre'),
            'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control('heading_text', [
            'label' => __('Votre titre'),
            'type' => Controls_Manager::TEXT,
            'default' => 'h2',
            'title' => __('Enter some text')
        ]);

        $this->add_control('heading_hn', [
            'label' => __('Balise HN'),
            'type' => Controls_Manager::SELECT,
            'default' => 'h2',
            'options' => [
                'h1' => __('h1'),
                'h2' => __('h2'),
                'h3' => __('h3'),
                'h4' => __('h4'),
                'h5' => __('h5'),
                'h6' => __('h6'),
                'p' => __('p')
            ]
        ]);

        $this->add_control('size_heading', [
            'label' => __('Taille'),
            'type' => Controls_Manager::SELECT,
            'default' => 'h2',
            'options' => [
                'h1' => __('h1'),
                'h2' => __('h2'),
                'h3' => __('h3'),
                'h4' => __('h4'),
                'h5' => __('h5'),
                'h6' => __('h6'),
            ]
        ]);

        $this->end_controls_section();

        $this->start_controls_section('sous-titre', [
            'label' => __('Sous-Titre'),
            'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control('subheading_text', [
            'label' => __('Votre sous-titre'),
            'type' => Controls_Manager::TEXT,
            'default' => '',
            'title' => __('Enter some text'),
        ]);

        $this->add_control('subheading_hn', [
            'label' => __('Balise HN'),
            'type' => Controls_Manager::SELECT,
            'default' => 'p',
            'options' => [
                'h1' => __('h1'),
                'h2' => __('h2'),
                'h3' => __('h3'),
                'h4' => __('h4'),
                'h5' => __('h5'),
                'h6' => __('h6'),
                'p' => __('p')
            ]
        ]);

        $this->add_control('subheading_size', [
            'label' => __('Taille'),
            'type' => Controls_Manager::SELECT,
            'default' => 'fz-base',
            'options' => [
                'h1' => __('h1'),
                'h2' => __('h2'),
                'h3' => __('h3'),
                'h4' => __('h4'),
                'h5' => __('h5'),
                'h6' => __('h6'),
                'fz-base' => __('fz-base')
            ]
        ]);

        $this->end_controls_section();

        $this->start_controls_section('category', [
            'label' => __('Catégorie de produits'),
            'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
        ]);

        $orderby = 'name';
        $order = 'asc';
        $hide_empty = false;
        $cat_args = array(
            'orderby' => $orderby,
            'order' => $order,
            'hide_empty' => $hide_empty,
        );

        $product_categories = get_terms('product_cat', $cat_args);

        if (is_array($product_categories)) {

            $choice_category_options = [];

            foreach ($product_categories as $product_category) {
                if ($product_category) {
                    $choice_category_options[$product_category->term_id] = $product_category->name;
                }
            }

            $this->add_control('choice_category', [
                'label' => __('Categories'),
                'type' => Controls_Manager::SELECT,
                'default' => $choice_category_options[15],
                'options' => $choice_category_options
            ]);

            $this->add_control('choice_number_product', [
               'label' => __('Nombre de produits à afficher'),
               'type' => Controls_Manager::NUMBER,
               'default' => -1
            ]);

        }

        $this->end_controls_section();
    }

    protected function render($instance = [])
    {
        $settings = $this->get_settings_for_display();

        $heading = $settings['heading_text'];
        $headingHn = !empty($settings['heading_hn']) ? $settings['heading_hn'] : 'h2';
        $headingSize = !empty($settings['size_heading']) ? $settings['size_heading'] : 'h2';

        $subHeading = $settings['subheading_text'];
        $subHeadingHn = !empty($settings['subheading_hn']) ? $settings['subheading_hn'] : 'h2';
        $subHeadingSize = !empty($settings['subheading_size']) ? $settings['subheading_size'] : 'fz-base';

        $getCategoryProducts = $settings['choice_category'];
        $getNumberProducts = $settings['choice_number_product'];

        echo '<div class="main-header">';
            echo '<' . $headingHn . ' class="' . $headingSize . '" >';
                echo $heading;
            echo '</' . $headingHn . '>';
            echo '<' . $subHeadingHn . ' class="' . $subHeadingSize . '" >';
                echo $subHeading;
            echo '</' . $subHeadingHn . '>';
        echo '</div>';

        $args = array(
            'post_type' => 'product',
            'post_status' => 'publish',
            'posts_per_page' => $getNumberProducts,
            'tax_query' => array(
                array(
                    'taxonomy' => 'product_cat',
                    'field' => 'term_id',
                    'terms' => $getCategoryProducts,
                    'operator' => 'IN'
                )
            )
        );

        $products = new \WP_Query($args);



        if ($products->have_posts()) {
            echo '<ul class="products columns-3">';
                while ($products->have_posts()) {
                    $products->the_post();
                    wc_get_template( 'content-product.php' );
                }
            echo '</ul>';
        }


    }

    protected function content_template()
    {
    }

    public function render_plain_content($instance = [])
    {
    }

}

Plugin::instance()->widgets_manager->register_widget_type(new Widget_Push_Products);
