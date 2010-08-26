<?php
/**
 * @category   Mad
 * @package    Mad_View
 * @subpackage UnitTests
 * @copyright  (c) 2007-2009 Maintainable Software, LLC
 * @license    http://opensource.org/licenses/bsd-license.php BSD 
 */

/**
 * Set environment
 */
if (!defined('MAD_ENV')) define('MAD_ENV', 'test');
if (!defined('MAD_ROOT')) {
    require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/config/environment.php';
}

class Mad_View_Helper_UrlTest_MockController extends Mad_Controller_Base
{
    public function getControllerName() { return 'mock'; }
}

/**
 * @group      view
 * @category   Mad
 * @package    Mad_View
 * @subpackage UnitTests
 * @copyright  (c) 2007-2009 Maintainable Software, LLC
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 */
class Mad_View_Helper_UrlTest extends Mad_Test_Unit
{
    public function setUp()
    {
        $controller = new Mad_View_Helper_UrlTest_MockController();
        $this->view = new Mad_View_Base($controller);
        $this->view->addHelper(new Mad_View_Helper_Url($this->view));
        $this->view->addHelper(new Mad_View_Helper_Tag($this->view));
        $this->view->addHelper(new Mad_View_Helper_Javascript($this->view));
    }

    public function testLinkTagWithStraightUrl()
    {
        $this->assertEquals('<a href="http://www.example.com">Hello</a>',
                            $this->view->linkTo('Hello', 'http://www.example.com'));
    }
    
    public function testLinkTagWithoutHostOption()
    {
        $this->assertEquals('<a href="/weblog/show">Test Link</a>',
                            $this->view->linkTo('Test Link', array('controller' => 'weblog', 'action' => 'show')));
    }

    public function testLinkTagWithHostOption()
    {
        $this->assertEquals('<a href="http://www.example.com/weblog/show">Test Link</a>',
                            $this->view->linkTo('Test Link', array('controller' => 'weblog', 'action' => 'show', 'host' => 'www.example.com')));
    }

    public function testLinkTagWithQuery()
    {
        $this->assertEquals('<a href="http://www.example.com?q1=v1&amp;q2=v2">Hello</a>',
                            $this->view->linkTo('Hello', 'http://www.example.com?q1=v1&amp;q2=v2'));
    }

    public function testLinkTagWithQueryAndNoName()
    {
        $this->assertEquals("<a href=\"http://www.example.com?q1=v1&amp;q2=v2\">http://www.example.com?q1=v1&amp;q2=v2</a>",
                            $this->view->linkTo(null, 'http://www.example.com?q1=v1&amp;q2=v2'));
    }

    public function testLinkTagWithImg()
    {
        $this->assertEquals("<a href=\"http://www.example.com\"><img src='/favicon.jpg' /></a>",
                            $this->view->linkTo("<img src='/favicon.jpg' />", "http://www.example.com"));
    }

    public function testLinkWithNilHtmlOptions()
    {
        $this->assertEquals("<a href=\"/mock/myaction\">Hello</a>",
                            $this->view->linkTo("Hello", array('action' => 'myaction'), null));
    }

    public function testLinkTagWithCustomOnclick()
    {
        $this->assertEquals("<a href=\"http://www.example.com\" onclick=\"alert('yay!')\">Hello</a>",
                            $this->view->linkTo("Hello", "http://www.example.com", array('onclick' => "alert('yay!')")));
    }

    public function testLinkTagWithJavascriptConfirm()
    {
        $this->assertEquals("<a href=\"http://www.example.com\" onclick=\"return confirm('Are you sure?');\">Hello</a>",
                            $this->view->linkTo("Hello", "http://www.example.com", array('confirm' => "Are you sure?")));
        $this->assertEquals("<a href=\"http://www.example.com\" onclick=\"return confirm('You can\\'t possibly be sure, can you?');\">Hello</a>",
                            $this->view->linkTo("Hello", "http://www.example.com", array('confirm' => "You can't possibly be sure, can you?")));
        $this->assertEquals("<a href=\"http://www.example.com\" onclick=\"return confirm('You can\\'t possibly be sure,\\n can you?');\">Hello</a>",
                            $this->view->linkTo("Hello", "http://www.example.com", array('confirm' => "You can't possibly be sure,\n can you?")));
    }

    public function testLinkTagUsingPostJavascript()
    {
        $this->assertEquals("<a href=\"http://www.example.com\" onclick=\"var f = document.createElement('form'); f.style.display = 'none'; this.parentNode.appendChild(f); f.method = 'POST'; f.action = this.href;f.submit();return false;\">Hello</a>",
                            $this->view->linkTo("Hello", "http://www.example.com", array('method' => 'post')));
    }

    public function testLinkTagUsingDeleteJavascript()
    {
        $this->assertEquals("<a href=\"http://www.example.com\" onclick=\"var f = document.createElement('form'); f.style.display = 'none'; this.parentNode.appendChild(f); f.method = 'POST'; f.action = this.href;var m = document.createElement('input'); m.setAttribute('type', 'hidden'); m.setAttribute('name', '_method'); m.setAttribute('value', 'delete'); f.appendChild(m);f.submit();return false;\">Destroy</a>",
                            $this->view->linkTo("Destroy", "http://www.example.com", array('method' => 'delete')));
    }

    public function testLinkTagUsingPostJavascriptAndConfirm()
    {
        $this->assertEquals("<a href=\"http://www.example.com\" onclick=\"if (confirm('Are you serious?')) { var f = document.createElement('form'); f.style.display = 'none'; this.parentNode.appendChild(f); f.method = 'POST'; f.action = this.href;f.submit(); };return false;\">Hello</a>",
                            $this->view->linkTo("Hello", "http://www.example.com", array('method' => 'post', 'confirm' => "Are you serious?")));
    }

    public function testLinkTagUsingDeleteJavascriptAndConfirm()
    {
        $this->assertEquals("<a href=\"/images/destroy/1\" onclick=\"if (confirm('Are you serious?')) { var f = document.createElement('form'); f.style.display = 'none'; this.parentNode.appendChild(f); f.method = 'POST'; f.action = this.href;var m = document.createElement('input'); m.setAttribute('type', 'hidden'); m.setAttribute('name', '_method'); m.setAttribute('value', 'delete'); f.appendChild(m);f.submit(); };return false;\">Destroy</a>",
                            $this->view->linkTo("Destroy", array('controller' => 'images', 'action' => 'destroy', 'id' => 1), array('method' => 'delete', 'confirm' => "Are you serious?")));
    }

    public function testLinkToUnless()
    {
        $this->assertEquals('Showing',
                            $this->view->linkToUnless(true, 'Showing', array('action' => 'show', 'controller' => 'weblog')));
        $this->assertEquals("<a href=\"/weblog/list\">Listing</a>", // @todo http://www.example.com
                            $this->view->linkToUnless(false, 'Listing', array('action' => 'list', 'controller' => 'weblog')));
        $this->assertEquals('Showing',
                            $this->view->linkToUnless(true, 'Showing', array('action' => 'show', 'controller' => 'weblog', 'id' => 1)));
    }

    public function testLinkToIf()
    {
        $this->assertEquals('Showing',
                            $this->view->linkToIf(false, 'Showing', array('action' => 'show', 'controller' => 'weblog')));
        $this->assertEquals("<a href=\"/weblog/list\">Listing</a>", // @todo http://www.example.com
                            $this->view->linkToIf(true, 'Listing', array('action' => 'list', 'controller' => 'weblog')));
        $this->assertEquals('Showing',
                            $this->view->linkToIf(false, 'Showing', array('action' => 'show', 'controller' => 'weblog', 'id' => 1)));
    }

    public function testMailTo()
    {
        $this->assertEquals("<a href=\"mailto:david@loudthinking.com\">david@loudthinking.com</a>",
                            $this->view->mailTo("david@loudthinking.com"));
        $this->assertEquals("<a href=\"mailto:david@loudthinking.com\">David Heinemeier Hansson</a>",
                            $this->view->mailTo("david@loudthinking.com", "David Heinemeier Hansson"));
        $this->assertEquals("<a class=\"admin\" href=\"mailto:david@loudthinking.com\">David Heinemeier Hansson</a>",
                            $this->view->mailTo("david@loudthinking.com", "David Heinemeier Hansson", array("class" => "admin")));
    }


    public function testMailToWithJavascript()
    {
        $this->assertEquals("<script type=\"text/javascript\">eval(unescape('%64%6f%63%75%6d%65%6e%74%2e%77%72%69%74%65%28%27%3c%61%20%68%72%65%66%3d%22%6d%61%69%6c%74%6f%3a%6d%65%40%64%6f%6d%61%69%6e%2e%63%6f%6d%22%3e%4d%79%20%65%6d%61%69%6c%3c%2f%61%3e%27%29%3b'))</script>",
                            $this->view->mailTo("me@domain.com", "My email", array('encode' => 'javascript')));
    }
    
    public function testMailWithOptions()
    {
        $this->assertEquals('<a href="mailto:me@example.com?cc=ccaddress%40example.com&amp;bcc=bccaddress%40example.com&amp;body=This%20is%20the%20body%20of%20the%20message.&amp;subject=This%20is%20an%20example%20email">My email</a>',
                            $this->view->mailTo("me@example.com", "My email", array('cc' => "ccaddress@example.com", 'bcc' => "bccaddress@example.com", 'subject' => "This is an example email", 'body' => "This is the body of the message.")));
    }
    
    public function testMailToWithImg()
    {
        $this->assertEquals('<a href="mailto:feedback@example.com"><img src="/feedback.png" /></a>',
                            $this->view->mailTo('feedback@example.com', '<img src="/feedback.png" />'));
    }
    
    public function testMailToWithHex()
    {
        $this->assertEquals("<a href=\"&#109;&#97;&#105;&#108;&#116;&#111;&#58;%6d%65@%64%6f%6d%61%69%6e.%63%6f%6d\">My email</a>",
                            $this->view->mailTo("me@domain.com", "My email", array('encode' => "hex")));
        $this->assertEquals("<a href=\"&#109;&#97;&#105;&#108;&#116;&#111;&#58;%6d%65@%64%6f%6d%61%69%6e.%63%6f%6d\">&#109;&#101;&#64;&#100;&#111;&#109;&#97;&#105;&#110;&#46;&#99;&#111;&#109;</a>",
                            $this->view->mailTo("me@domain.com", null, array('encode' => "hex")));
    }
    
    public function testMailToWithReplaceOptions()
    {
        $this->assertEquals("<a href=\"mailto:wolfgang@stufenlos.net\">wolfgang(at)stufenlos(dot)net</a>",
                            $this->view->mailTo("wolfgang@stufenlos.net", null, array('replaceAt' => "(at)", 'replaceDot' => "(dot)")));
        $this->assertEquals("<a href=\"&#109;&#97;&#105;&#108;&#116;&#111;&#58;%6d%65@%64%6f%6d%61%69%6e.%63%6f%6d\">&#109;&#101;&#40;&#97;&#116;&#41;&#100;&#111;&#109;&#97;&#105;&#110;&#46;&#99;&#111;&#109;</a>",
                            $this->view->mailTo("me@domain.com", null, array('encode' => "hex", 'replaceAt' => "(at)")));
        $this->assertEquals("<a href=\"&#109;&#97;&#105;&#108;&#116;&#111;&#58;%6d%65@%64%6f%6d%61%69%6e.%63%6f%6d\">My email</a>",
                            $this->view->mailTo("me@domain.com", "My email", array('encode' => "hex", 'replaceAt' => "(at)")));
        $this->assertEquals("<a href=\"&#109;&#97;&#105;&#108;&#116;&#111;&#58;%6d%65@%64%6f%6d%61%69%6e.%63%6f%6d\">&#109;&#101;&#40;&#97;&#116;&#41;&#100;&#111;&#109;&#97;&#105;&#110;&#40;&#100;&#111;&#116;&#41;&#99;&#111;&#109;</a>",
                            $this->view->mailTo("me@domain.com", null, array('encode' => "hex", 'replaceAt' => "(at)", 'replaceDot' => "(dot)")));
    }
}
