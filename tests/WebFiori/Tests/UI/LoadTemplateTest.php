<?php
namespace WebFiori\Tests\UI;

use PHPUnit\Framework\TestCase;
use WebFiori\UI\HeadNode;
use WebFiori\UI\HTMLDoc;
use WebFiori\UI\HTMLNode;
use WebFiori\UI\TemplateCompiler;
/**
 * Description of TestLoadTemplate
 *
 * @author Ibrahim
 */
class LoadTemplateTest extends TestCase {
    const TEST_TEMPLATES_PATH = ROOT.DIRECTORY_SEPARATOR.'tests'.DIRECTORY_SEPARATOR.'test-templates'.DIRECTORY_SEPARATOR;
    /**
     * @test
     */
    public function test00() {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Empty template path');
        $compiler = new TemplateCompiler('');
    }
    /**
     * @test
     */
    public function test01() {
        $compiler = new TemplateCompiler(self::TEST_TEMPLATES_PATH.'t00.html');
        $node = $compiler->getCompiled();
        $this->assertTrue($node instanceof HTMLDoc);
        $this->assertEquals(3, $node->getHeadNode()->childrenCount());
        $this->assertEquals('TODO supply a title', $node->getHeadNode()->getPageTitle());
        $this->assertEquals('UTF-8', $node->getHeadNode()->getCharSet());
        $this->assertEquals(1, $node->getBody()->childrenCount());
        $this->assertEquals('TODO write content', $node->getBody()->getChild(0)->getChild(0)->getText());
    }
    /**
     * @test
     */
    public function test03() {
        $compiler = new TemplateCompiler(self::TEST_TEMPLATES_PATH.'vue-00.html', [
            'vue-src' => 'https://cdn.jsdelivr.net/npm/vue',
            'php-message' => 'This is PHP var.'
        ]);
        $node = $compiler->getCompiled();
        $this->assertEquals('This is PHP var.', $node->getChildByID('php-message')->getChild(0)->getText());
        $this->assertEquals('{{ message }}', $node->getChildByID('vue-message')->getChild(0)->getText());
    }
    /**
     * @test
     */
    public function test02() {
        $compiler = new TemplateCompiler(self::TEST_TEMPLATES_PATH.'t01.html', [
            'title' => 'Users Status',
            'desc' => 'A page that shows the status of users accounts.',
            'top-paragraph' => 'All users.',
            'username' => 'The username of the user.',
            'email' => 'The email of the user.',
            'ajax lib' => 'https://example.com/ajaxlib.js'
        ]);
        $node = $compiler->getCompiled();
        $this->assertTrue($node instanceof HTMLDoc);
        $this->assertEquals(5, $node->getHeadNode()->childrenCount());
        $this->assertEquals('https://example.com/ajaxlib.js', $node->getChildByID('my-script')->getAttribute('src'));
        $this->assertEquals('Users Status', $node->getHeadNode()->getPageTitle());
        $this->assertEquals('A page that shows the status of users accounts.', $node->getHeadNode()->getMeta('description')->getAttribute('content'));
        $this->assertEquals('Users Status', $node->getChildByID('h-title')->getChild(0)->getText());
        $headerRow = $node->getChildByID('header-row');
        $this->assertEquals('The username of the user.', $headerRow->getChild(0)->getChild(0)->getText());
        $this->assertEquals('The email of the user.', $headerRow->getChild(1)->getChild(0)->getText());
        $this->assertEquals('{{account status}}', $headerRow->getChild(2)->getChild(0)->getText());
    }
    /**
     * @test
     */
    public function test04() {
        $compiler = new TemplateCompiler(self::TEST_TEMPLATES_PATH.'super-00.html');
        $this->assertEquals('object', gettype($compiler->getCompiled()));
    }
    /**
     * @test
     */
    public function test05() {
        $compiler = new TemplateCompiler(self::TEST_TEMPLATES_PATH.'php-template.php');
        $node = $compiler->getCompiled();
        $this->assertEquals("<div>\r\n"
                . "    This is a test on php</div>", $node->toHTML());
    }
    /**
     * @test
     */
    public function test06() {
        $compiler = new TemplateCompiler(self::TEST_TEMPLATES_PATH.'php-template-2.php', [
            'posts' => [
                'One',
                'Two',
                'Three'
            ]]);
        $this->assertEquals("<div>"
                . "<ul>"
                . "<li>One</li>"
                . "<li>Two</li>"
                . "<li>Three</li>"
                . "</ul>"
                . "</div>", $compiler->getCompiled()->toHTML());
    }
    /**
     * @test
     */
    public function test07() {
        $compiler = new TemplateCompiler(self::TEST_TEMPLATES_PATH.'php-template-2.php', [
            'posts' => []
        ]);
        $node = $compiler->getCompiled();
        $this->assertEquals("<div>\r\n"
                . "    No posts.\r\n"
                . "</div>", $node->toHTML());
    }
    
    /**
     * @test
     */
    public function test08() {
        $compiler = new TemplateCompiler('template.php', [
            'message' => 'Good Job!',
            'posts' => [
                'One',
                'Two',
                'Three'
            ]]);
        $this->assertEquals("<div>"
                . "<ul>"
                . "<li>One</li>"
                . "<li>Two</li>"
                . "<li>Three</li>"
                . "</ul>"
                . "<div>\r\n"
                . "    Good Job!"
                . "</div>"
                . "</div>", $compiler->getCompiled()->toHTML());
    }
    /**
     * @test
     */
    public function test09() {
        $compiler = new TemplateCompiler('template.html');
        $this->assertEquals("<div v-if=\"someVar <= 6 || someVar >= 8 || someVar === 6\">\r\n"
                . "    <script>\r\n"
                . "        \r\n"
                . "        function allIsGood() {\r\n"
                . "            if (a > 6) {\r\n"
                . "                alert(\"Oh. A is > 6 but probably < 100.\");\r\n"
                . "            }\r\n"
                . "            if (a < 100) {\r\n"
                . "                alert('Oh. A is < 100.');\r\n"
                . "            }\r\n"
                . "        }\r\n"
                . "    \r\n"
                . "    </script>\r\n"
                . "</div>\r\n", $compiler->getCompiled()->toHTML(true));
    }
    /**
     * @test
     */
    public function test10() {
        $compiler = new TemplateCompiler('template2.php');
        $this->assertEquals("<div v-if=\"someVar <= 6 || someVar >= 8 || someVar === 6\">\r\n"
                . "    <script>\r\n"
                . "        \r\n"
                . "        function allIsGood() {\r\n"
                . "            if (a > 6) {\r\n"
                . "                alert(\"Oh. A is > 6 but probably < 100.\");\r\n"
                . "            }\r\n"
                . "            if (a < 100) {\r\n"
                . "                alert('Oh. A is < 100.');\r\n"
                . "            }\r\n"
                . "        }\r\n"
                . "    \r\n"
                . "    </script>\r\n"
                . "</div>\r\n", $compiler->getCompiled()->toHTML(true));
    }
    /**
     * @test
     */
    public function testHeadTemplate00() {
        $c = new TemplateCompiler(self::TEST_TEMPLATES_PATH.'head-template-00.html');
        $node = $c->getCompiled();
        $this->assertTrue($node instanceof HeadNode);
        $this->assertEquals(3, $node->childrenCount());
        $this->assertEquals('TODO supply a title', $node->getPageTitle());
        $this->assertEquals('UTF-8', $node->getCharSet());
    }
    /**
     * @test
     */
    public function testHeadTemplate01() {
        $c = new TemplateCompiler(self::TEST_TEMPLATES_PATH.'head-template-01.html');
        $node = $c->getCompiled();
        $this->assertTrue($node instanceof HeadNode);
        $this->assertEquals(3, $node->childrenCount());
        $this->assertEquals('{{title}}', $node->getPageTitle());
        $this->assertEquals('UTF-8', $node->getCharSet());
    }
    /**
     * @test
     */
    public function test11() {
        $this->expectException(\webfiori\ui\exceptions\TemplateNotFoundException::class);
        $compiler = new TemplateCompiler(self::TEST_TEMPLATES_PATH.'not-exist.php');
    }
    /**
     * @test
     */
    public function testHeadTemplate02() {
        $c = new TemplateCompiler(self::TEST_TEMPLATES_PATH.'head-template-01.html', [
            'title' => 'This is page title.'
        ]);
        $node = $c->getCompiled();
        $this->assertTrue($node instanceof HeadNode);
        $this->assertEquals(3, $node->childrenCount());
        $this->assertEquals('This is page title.', $node->getPageTitle());
        $this->assertEquals('UTF-8', $node->getCharSet());
    }
    /**
     * @test
     */
    public function testHeadTemplate03() {
        $c = new TemplateCompiler(self::TEST_TEMPLATES_PATH.'head-template-02.html', [
            'title' => 'This is page title.',
            'description'=>'This is the description of the page.'
        ]);
        $node = $c->getCompiled();
        $this->assertTrue($node instanceof HeadNode);
        $this->assertEquals(4, $node->childrenCount());
        $this->assertEquals('This is page title.', $node->getPageTitle());
        $this->assertEquals('This is the description of the page.', $node->getMeta('description')->getAttributeValue('content'));
        $this->assertEquals('UTF-8', $node->getCharSet());
    }
    /**
     * @test
     */
    public function testHeadTemplate04() {
        $c = new TemplateCompiler(self::TEST_TEMPLATES_PATH.'head-template-03.html');
        $node = $c->getCompiled();
        $this->assertTrue($node instanceof HeadNode);
        $this->assertEquals(6, $node->childrenCount());
        $this->assertEquals('{{title}}', $node->getPageTitle());
        $this->assertEquals('{{description}}', $node->getMeta('description')->getAttributeValue('content'));
        $this->assertEquals('UTF-8', $node->getCharSet());
    }
    /**
     * @test
     */
    public function testAddChildFromTemplate00() {
        $node = new HTMLNode();
        $node->include(self::TEST_TEMPLATES_PATH.'component-00.html', [
            'base' => 'https://example.com',
            'home-label' => 'Home Page',
            'about-label' => 'About Us',
            'contact-label' => 'Contact Us'
        ]);
        $this->assertEquals(1, $node->childrenCount());
        $this->assertEquals('ul', $node->getChild(0)->getChild(0)->getNodeName());
        $this->assertEquals('https://example.com/contact-us', $node->getChild(0)->getChild(0)->getChild(2)->getChild(0)->getAttribute('href'));
        $this->assertEquals('About Us', $node->getChild(0)->getChild(0)->getChild(1)->getChild(0)->getChild(0)->getText());
    }
    /**
     * @test
     */
    public function testAddChildFromTemplate01() {
        $node = new HTMLNode();
        $node->component(self::TEST_TEMPLATES_PATH.'component-01.html', [
            'base' => 'https://example.com',
            'home-label' => 'Home Page',
            'about-label' => 'About Us',
            'contact-label' => 'Contact Us',
            'social-media-links' => [
                'twitter-link' => 'https://twitter.com/_webfiori',
                'linkedin-link' => 'https://www.linkedin.com/in/ibrahim-binalshikh/'
            ]
        ]);
        $this->assertEquals(2, $node->childrenCount());
        $this->assertEquals('ul', $node->getChild(0)->getChild(0)->getNodeName());
        $this->assertEquals('https://example.com/contact-us', $node->getChild(0)->getChild(0)->getChild(2)->getChild(0)->getAttribute('href'));
        $this->assertEquals('About Us', $node->getChild(0)->getChild(0)->getChild(1)->getChild(0)->getChild(0)->getText());
        $this->assertEquals('footer', $node->getChild(1)->getNodeName());
        $this->assertEquals('https://twitter.com/_webfiori', $node->getChild(1)->getChild(0)->getChild(0)->getChild(0)->getAttribute('href'));
        $this->assertEquals('https://www.linkedin.com/in/ibrahim-binalshikh/', $node->getChild(1)->getChild(0)->getChild(1)->getChild(0)->getAttribute('href'));
    }
}
