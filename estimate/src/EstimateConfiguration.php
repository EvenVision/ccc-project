<?php

namespace Drupal\estimate;

use Drupal\block\Entity\Block;
use Drupal\block_content\Entity\BlockContent;
use Drupal\block_content\Entity\BlockContentType;
use Drupal\Core\Url;
use Drupal\menu_link_content\Entity\MenuLinkContent;
use Drupal\node\Entity\Node;

class EstimateConfiguration {

    public static function createPages ()
    {
        // creating Url to contact form "Send us a message"
        $url = new Url('entity.contact_form.canonical', ['contact_form' => "send_us_a_message"]);
        $contact_form_send_us_a_message_url = $url->setAbsolute()->toString();

        // Create get support page
        $bodyHtmlGetSupport = '
            <div class="body-get-support">
            <div class="container-get-support">
            <div class="block-image">
            <div class="bg-image">
            <div class="image-phone">&nbsp;</div>
            </div>
            </div>
            
            <div class="block-body">
            <div class="block-body-text">
            <p>Give Us a Call</p>
            
            <div><a class="estimate-button" href="tel:2066501008">CALL NOW</a></div>
            </div>
            </div>
            </div>
            
            <div class="container-get-support">
            <div class="block-image">
            <div class="bg-image">
            <div class="image-email">&nbsp;</div>
            </div>
            </div>
            
            <div class="block-body">
            <div class="block-body-text">
            <p>Send Us a Message</p>
            
            <div><a class="estimate-button" href="'.$contact_form_send_us_a_message_url.'">CALL NOW</a></div>
            </div>
            </div>
            </div>
            </div>
        ';
        $body1[] = [
            'value' => $bodyHtmlGetSupport,
            'format' => 'full_html',
        ];

        $nodeGetSupportPage = Node::create([
            'type'                 => 'page',
            'title'                => 'Get Support',
            'body'                 => $body1,
        ]);
        $nodeGetSupportPage->set('path', '/get-support');
        $nodeGetSupportPage->save();

        // Create home page
        $bodyHtmlHomePage = '
            <div class="container">
            <div class="region_head_text">
            <h2><strong>We show you the hidden opportunities to improve your results now &amp; grow the profits of your body shops.</strong></h2>
            </div>
            <div class="list_item">
            <div class="list_number">1</div>
            <div class="list_text">See the metrics insurers use to grade your shop and optimize them.</div>
            </div>
            <div class="list_item">
            <div class="list_number">2</div>
            <div class="list_text">Find the trouble spots that commonly cause friction in body shop operations.</div>
            </div>
            <div class="list_item">
            <div class="list_number">3</div>
            <div class="list_text">See actionable efficiency measures for your employees &amp; operation so you can stop leaking cash fast &amp; grow your profits.</div>
            </div>
            <div class="region_button"><a class="estimate-button" href="/user/register">REGISTER NOW</a>&nbsp;</div>
            </div>
            ';
        $body2[] = [
            'value' => $bodyHtmlHomePage,
            'format' => 'full_html',
        ];

        $nodeHomePage = Node::create([
            'type'                 => 'page',
            'title'                => 'Home Page',
            'body'                 => $body2,
        ]);
        $nodeHomePage->set('path', '/home-page');
        $nodeHomePage->save();

        // To change the front page
        \Drupal::configFactory()->getEditable('system.site')->set('page.front', '/home-page')->save();
    }

    // create custom block
    // admin/structure/block/block-content
    public static function createCustomBlock ()
    {
        // create "estimate" block type
        $estimateBlockType = BlockContentType::create([
                    'id' => 'estimate',
                    'label' => 'Estimate block type',
                    'description' => "A estimate block type",
                    ]);
        $estimateBlockType->save();
        block_content_add_body_field($estimateBlockType->id());

        // create block Copyright
        $blockCopyright = BlockContent::create([
            'info' => 'Copyright Collision Dashboard',
            'type' => 'estimate',
            'langcode' => 'en',
            'body' => [
                'value' => '<p>Copyright &copy; Collision Dashboard 2022</p>',
                'format' => 'full_html',
            ],
        ]);
        $blockCopyright->save();

        $placed_block1 = Block::create([
            'id' => 'estimate_theme_copyright_collision_dashboard',
            'theme' => 'estimate_theme',
            'weight' => -6,
            'status' => TRUE,
            'region' => 'copyright',
            'plugin' => 'block_content:' . $blockCopyright->uuid(),
        ]);
        $placed_block1->save();

        // create block Created by EnVision
        $blockCreatedBy = BlockContent::create([
            'info' => 'Created by EnVision',
            'type' => 'estimate',
            'langcode' => 'en',
            'body' => [
                'value' => '<p>Created by EvenVision</p>',
                'format' => 'full_html',
            ],
        ]);
        $blockCreatedBy->save();

        $placed_block2 = Block::create([
            'id' => 'estimate_theme_created_by_evenvision',
            'theme' => 'estimate_theme',
            'weight' => -5,
            'status' => TRUE,
            'region' => 'copyright',
            'plugin' => 'block_content:' . $blockCreatedBy->uuid(),
        ]);
        $placed_block2->save();

        // create block Home Page Title
        $blockHomePageTitle = BlockContent::create([
            'info' => 'Home Page Title',
            'type' => 'estimate',
            'langcode' => 'en',
            'body' => [
                'value' => '
                    <div class="section-title-home-page height-title-530px align-items-center">
                    <div class="block-right">
                    <div class="body-title-register">
                    <h1>Find opportunities for more profit in your shop. Grow your income now.</h1>
                    <p><a class="estimate-button" href="/user/register">REGISTER NOW</a></p>
                    </div>
                    </div>
                    </div>',
                'format' => 'full_html',
            ],
        ]);
        $blockHomePageTitle->save();

        $placed_block3 = Block::create([
            'id' => 'estimate_theme_home_page_title',
            'theme' => 'estimate_theme',
            'weight' => 0,
            'status' => TRUE,
            'region' => 'title',
            'plugin' => 'block_content:' . $blockHomePageTitle->uuid(),
            'visibility' => [
                'request_path' => [
                    'id' => 'request_path',
                    'negate' => FALSE,
                    'pages' => '<front>',
                ],
            ],
        ]);
        $placed_block3->save();

        // create block Register Page Title
        $blockRegisterPageTitle = BlockContent::create([
            'info' => 'Register Page Title',
            'type' => 'estimate',
            'langcode' => 'en',
            'body' => [
                'value' => '
                    <div class="section-title-register-page height-title-530px align-items-center">
                    <div class="block-left">&nbsp;</div>
                    <div class="block-right">
                    <div class="body-title-register">
                    <h1>Register</h1>
                    <p>Collision Dashboard will give you clear actionable metrics that allow you to grow your income, find &amp; smooth out friction points in your operation, and improve the most important numbers that insurers use to evaluate your shop.</p>
                    <p>Register below to get started!</p>
                    </div>
                    </div>
                    </div>',
                'format' => 'full_html',
            ],
        ]);
        $blockRegisterPageTitle->save();

        $placed_block4 = Block::create([
            'id' => 'estimate_theme_register_page_title',
            'theme' => 'estimate_theme',
            'weight' => 0,
            'status' => TRUE,
            'region' => 'title',
            'plugin' => 'block_content:' . $blockRegisterPageTitle->uuid(),
            'visibility' => [
                'request_path' => [
                    'id' => 'request_path',
                    'negate' => FALSE,
                    'pages' => '/user/register',
                ],
            ],
        ]);
        $placed_block4->save();

        // create block get support
        $blockGetSupport = BlockContent::create([
            'info' => 'Get Support',
            'type' => 'estimate',
            'langcode' => 'en',
            'body' => [
                'value' => '
                    <div class="section-title-register-page height-title-358px align-items-center">
                    <div class="block-right">
                    <h1>Get Support</h1>
                    </div>
                    </div>',
                'format' => 'full_html',
            ],
        ]);
        $blockGetSupport->save();

        $placed_block5 = Block::create([
            'id' => 'estimate_theme_get_support',
            'theme' => 'estimate_theme',
            'weight' => 0,
            'status' => TRUE,
            'region' => 'title',
            'plugin' => 'block_content:' . $blockGetSupport->uuid(),
            'visibility' => [
                'request_path' => [
                    'id' => 'request_path',
                    'negate' => FALSE,
                    'pages' => '/get-support',
                ],
            ],
        ]);
        $placed_block5->save();

        // create block Contact Us
        $blockContactUs = BlockContent::create([
            'info' => 'Contact Us',
            'type' => 'estimate',
            'langcode' => 'en',
            'body' => [
                'value' => '
                    <div class="section-title-home-page height-title-358px align-items-center">
                    <div class="block-right">
                    <h1>Contact Us</h1>
                    </div>
                    </div',
                'format' => 'full_html',
            ],
        ]);
        $blockContactUs->save();

        $placed_block6 = Block::create([
            'id' => 'estimate_theme_contact_us',
            'theme' => 'estimate_theme',
            'weight' => 0,
            'status' => TRUE,
            'region' => 'title',
            'plugin' => 'block_content:' . $blockContactUs->uuid(),
            'visibility' => [
                'request_path' => [
                    'id' => 'request_path',
                    'negate' => FALSE,
                    'pages' => '/contact/contact_us',
                ],
            ],
        ]);
        $placed_block6->save();
    }

    public static function createMenu ()
    {
        // create user account menu
        \Drupal::entityTypeManager()
            ->getStorage('menu')
            ->create([
                'id' => 'estimate-user-account-menu',
                'label' => 'Estimate User account menu',
                'description' => 'Estimate menu',
            ])
            ->save();

        $menu_link_1 = MenuLinkContent::create([
            'title' => 'Dashboard',
            'link' => ['uri' => 'internal:/estimate-view'],
            'menu_name' => 'estimate-user-account-menu',
            'expanded' => TRUE,
            'weight' => 0,
        ]);
        $menu_link_1->save();

        $menu_link_2 = MenuLinkContent::create([
            'title' => 'My account',
            'link' => ['uri' => 'internal:/user'],
            'menu_name' => 'estimate-user-account-menu',
            'expanded' => TRUE,
            'weight' => 1,
        ]);
        $menu_link_2->save();

        $menu_link_3 = MenuLinkContent::create([
            'title' => 'Log Out',
            'link' => ['uri' => 'internal:/user/logout'],
            'menu_name' => 'estimate-user-account-menu',
            'expanded' => TRUE,
            'weight' => 3,
        ]);
        $menu_link_3->save();

        // create main navigation menu
        \Drupal::entityTypeManager()
            ->getStorage('menu')
            ->create([
                'id' => 'estimate-main-navigation-menu',
                'label' => 'Estimate Main navigation menu',
                'description' => 'Estimate menu',
            ])
            ->save();

        $menu_link_1 = MenuLinkContent::create([
            'title' => 'Support',
            'link' => ['uri' => 'internal:/get-support'],
            'menu_name' => 'estimate-main-navigation-menu',
            'expanded' => TRUE,
            'weight' => 0,
        ]);
        $menu_link_1->save();

        $menu_link_2 = MenuLinkContent::create([
            'title' => 'Register Now',
            'link' => ['uri' => 'internal:/user/register'],
            'menu_name' => 'estimate-main-navigation-menu',
            'expanded' => TRUE,
            'weight' => 1,
        ]);
        $menu_link_2->save();

        $menu_link_3 = MenuLinkContent::create([
            'title' => 'Log In',
            'link' => ['uri' => 'internal:/user/login'],
            'menu_name' => 'estimate-main-navigation-menu',
            'expanded' => TRUE,
            'weight' => 2,
        ]);
        $menu_link_3->save();

        // create footer menu
        \Drupal::entityTypeManager()
            ->getStorage('menu')
            ->create([
                'id' => 'estimate-footer-menu',
                'label' => 'Estimate Footer menu',
                'description' => 'Estimate menu',
            ])
            ->save();

        $menu_link_4 = MenuLinkContent::create([
            'title' => 'Contact',
            'link' => ['uri' => 'internal:/contact/contact_us'],
            'menu_name' => 'estimate-footer-menu',
            'expanded' => TRUE,
        ]);
        $menu_link_4->save();

        $menu_link_5 = MenuLinkContent::create([
            'title' => 'Log In',
            'link' => ['uri' => 'internal:/user/login'],
            'menu_name' => 'estimate-footer-menu',
            'expanded' => TRUE,
        ]);
        $menu_link_5->save();

        $menu_link_6 = MenuLinkContent::create([
            'title' => 'Log Out',
            'link' => ['uri' => 'internal:/user/logout'],
            'menu_name' => 'estimate-footer-menu',
            'expanded' => TRUE,
        ]);
        $menu_link_6->save();

        $EstimateMainNavigationMenu = array(
            'id' => 'estimate-main-navigation-menu',
            'plugin' => 'system_menu_block:estimate-main-navigation-menu',
            'region' => 'navigation_main',
            'theme' => 'estimate_theme',
            'visibility' => array(),
            'weight' => 0,
        );

        $blockEstimateMainNavigationMenu = \Drupal\block\Entity\Block::create($EstimateMainNavigationMenu);
        $blockEstimateMainNavigationMenu->save();

        $EstimateUserAccountMenu = array(
            'id' => 'estimate-user-account-menu',
            'plugin' => 'system_menu_block:estimate-user-account-menu',
            'region' => 'navigation_additional',
            'theme' => 'estimate_theme',
            'visibility' => array(),
            'weight' => 0,
        );

        $blockEstimateUserAccountMenu = \Drupal\block\Entity\Block::create($EstimateUserAccountMenu);
        $blockEstimateUserAccountMenu->save();

        $EstimateFooterMenu = array(
            'id' => 'estimate-footer-menu',
            'plugin' => 'system_menu_block:estimate-footer-menu',
            'region' => 'footer',
            'theme' => 'estimate_theme',
            'visibility' => array(),
            'weight' => 0,
        );

        $blockEstimateFooterMenu = \Drupal\block\Entity\Block::create($EstimateFooterMenu);
        $blockEstimateFooterMenu->save();

    }

    public static function removeCustomBlock ()
    {
        // deleting blocks
        $storage_handler = \Drupal::entityTypeManager()->getStorage("block_content");
        $blocks = $storage_handler->loadByProperties(["type" => 'estimate']);

        foreach ($blocks as $block){
            if ($block->bundle() === 'estimate' && $block_content = \Drupal::service('entity.repository')->loadEntityByUuid('block_content', $block->uuid())){
                $block_content->delete();
            }
        }

        // deleting "estimate" block type
        $content_type = \Drupal::entityTypeManager()->getStorage('block_content_type')->load('estimate');
        if ($content_type){
            $content_type->delete();
        }

    }

    public static function removeMenu ()
    {
        // deleting items menu
        $menu_names = array('estimate-footer-menu','estimate-main-navigation-menu','estimate-user-account-menu');
        foreach ($menu_names as $menu_name){
            $mids = \Drupal::entityQuery('menu_link_content')
                ->condition('menu_name', $menu_name)
                ->execute();
            if (!empty($mids)) {
                $controller = \Drupal::entityTypeManager()->getStorage('menu_link_content');
                $entities = $controller->loadMultiple($mids);
                $controller->delete($entities);
            }

            $storage_handler = \Drupal::entityTypeManager()->getStorage("menu");
            $menus = $storage_handler->loadByProperties(["id" => $menu_name]);
            $storage_handler->delete($menus);

            foreach ($menus as $menu){
                if ($block_content = \Drupal::service('entity.repository')->loadEntityByUuid('menu', $menu->uuid())){
                    $block_content->delete();
                }
            }
        }
    }

    public static function removeForm ()
    {
        $formIds = array('contact_us','send_us_a_message');
        foreach ($formIds as $formid){
            $content_type = \Drupal::entityTypeManager()->getStorage('contact_form')->load($formid);
            if ($content_type){
                $content_type->delete();
            }
        }
    }
}