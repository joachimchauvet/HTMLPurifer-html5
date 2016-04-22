<?php
/**
 * Load HTMLPurifier with HTML5, TinyMCE, YouTube, Video support.
 *
 * Copyright
 * 2015 Joachim Chauveheid (https://github.com/Masterjoa/HTMLPurifer-html5)
 * 2014 Alex Kennberg (https://github.com/kennberg/php-htmlpurifier-html5)
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */


function htmlpurifier_config_html5($allowed = array(
    'img[src|alt|title|width|height|style|data-mce-src|data-mce-json]',
    'figure', 'figcaption',
    'video[src|type|width|height|poster|preload|controls]', 'source[src|type]',
    'a[href|target]',
    'iframe[width|height|src|frameborder|allowfullscreen]',
    'strong', 'b', 'i', 'u', 'em', 'br', 'font',
    'h1[style]', 'h2[style]', 'h3[style]', 'h4[style]', 'h5[style]', 'h6[style]',
    'p[style]', 'div[style]', 'center', 'address[style]',
    'span[style]', 'pre[style]',
    'ul', 'ol', 'li',
    'table[width|height|border|style]', 'th[width|height|border|style]',
    'tr[width|height|border|style]', 'td[width|height|border|style]',
    'hr'
)) {
    $config = HTMLPurifier_Config::createDefault();
    $config->set('HTML.Doctype', 'HTML 4.01 Transitional');
    $config->set('CSS.AllowTricky', true);
    $config->set('Cache.SerializerPath', '/tmp');

    // Allow wysihtml classes
//    $config->set('Attr.AllowedClasses', "font-size-smaller, font-size-larger, font-size-xx-large, font-size-x-large, font-size-large, font-size-medium, font-size-small, font-size-x-small, .font-size-xx-small{font-size:xx-small;}, text-align-right, text-align-center, text-align-left, text-align-justify, wysiwyg-float-left, wysiwyg-float-right, wysiwyg-clear-right, wysiwyg-clear-left");

    $config->set('AutoFormat.RemoveEmpty', true);
    $config->set('AutoFormat.RemoveSpansWithoutAttributes', true);

    // Allow iframes from: YouTube.com, Vimeo.com
    // TODO: add other websites
    $config->set('HTML.SafeIframe', true);
    $config->set('URI.SafeIframeRegexp', '%^(http:|https:)?//(www.youtube(?:-nocookie)?.com/embed/|player.vimeo.com/video/)%');

    $config->set('HTML.Allowed', implode(',', $allowed));

    // Set some HTML5 properties
    $config->set('HTML.DefinitionID', 'html5-definitions'); // unqiue id
    $config->set('HTML.DefinitionRev', 1);

    if ($def = $config->maybeGetRawHTMLDefinition()) {
        // http://developers.whatwg.org/sections.html
        $def->addElement('section', 'Block', 'Flow', 'Common');
        $def->addElement('nav',     'Block', 'Flow', 'Common');
        $def->addElement('article', 'Block', 'Flow', 'Common');
        $def->addElement('aside',   'Block', 'Flow', 'Common');
        $def->addElement('header',  'Block', 'Flow', 'Common');
        $def->addElement('footer',  'Block', 'Flow', 'Common');

        // Content model actually excludes several tags, not modelled here
        $def->addElement('address', 'Block', 'Flow', 'Common');
        $def->addElement('hgroup', 'Block', 'Required: h1 | h2 | h3 | h4 | h5 | h6', 'Common');

        // http://developers.whatwg.org/grouping-content.html
        $def->addElement('figure', 'Block', 'Optional: (figcaption, Flow) | (Flow, figcaption) | Flow', 'Common');
        $def->addElement('figcaption', 'Inline', 'Flow', 'Common');

        // http://developers.whatwg.org/the-video-element.html#the-video-element
        $def->addElement('video', 'Block', 'Optional: (source, Flow) | (Flow, source) | Flow', 'Common', array(
            'src' => 'URI',
            'type' => 'Text',
            'width' => 'Length',
            'height' => 'Length',
            'poster' => 'URI',
            'preload' => 'Enum#auto,metadata,none',
            'controls' => 'Bool',
        ));
        $def->addElement('source', 'Block', 'Flow', 'Common', array(
            'src' => 'URI',
            'type' => 'Text',
        ));

        // http://developers.whatwg.org/text-level-semantics.html
        $def->addElement('s',    'Inline', 'Inline', 'Common');
        $def->addElement('var',  'Inline', 'Inline', 'Common');
        $def->addElement('sub',  'Inline', 'Inline', 'Common');
        $def->addElement('sup',  'Inline', 'Inline', 'Common');
        $def->addElement('mark', 'Inline', 'Inline', 'Common');
        $def->addElement('wbr',  'Inline', 'Empty', 'Core');

        // http://developers.whatwg.org/edits.html
        $def->addElement('ins', 'Block', 'Flow', 'Common', array('cite' => 'URI', 'datetime' => 'CDATA'));
        $def->addElement('del', 'Block', 'Flow', 'Common', array('cite' => 'URI', 'datetime' => 'CDATA'));

        // TinyMCE
        $def->addAttribute('img', 'data-mce-src', 'Text');
        $def->addAttribute('img', 'data-mce-json', 'Text');

        // Others
        $def->addAttribute('iframe', 'allowfullscreen', 'Bool');
        $def->addAttribute('table', 'height', 'Text');
        $def->addAttribute('td', 'border', 'Text');
        $def->addAttribute('th', 'border', 'Text');
        $def->addAttribute('tr', 'width', 'Text');
        $def->addAttribute('tr', 'height', 'Text');
        $def->addAttribute('tr', 'border', 'Text');
    }

    return $config;
}
