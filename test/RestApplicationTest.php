<?php
namespace Mooti\Test\Xizlr\Core;

require dirname(__FILE__).'/../vendor/autoload.php';

use Mooti\Xizlr\Core\RestApplication;
use Mooti\Xizlr\Core\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Interop\Container\ContainerInterface;
use League\Route\RouteCollection;
use League\Route\Dispatcher;
use Mooti\Xizlr\Core\BaseController;

class RestApplicationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function createRequestSucceeds()
    {
        $restApplication = $this->getMockBuilder(RestApplication::class)
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();

        self::assertInstanceOf(Request::class, $restApplication->createRequest());
    }

    /**
     * @test
     */
    public function registerServicesSucceeds()
    {
        $arrayObject = new \ArrayObject;
        $restApplication = $this->getMockBuilder(RestApplication::class)
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();

        self::assertSame($arrayObject, $restApplication->registerServices($arrayObject));
        self::assertCount(2, $arrayObject);
    }

    /**
     * @test
     */
    public function runServicesSucceeds()
    {
        $requestMethod = 'GET';
        $requestPath   = '/test';

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $request->expects(self::once())
            ->method('getMethod')
            ->will(self::returnValue($requestMethod));

        $request->expects(self::once())
            ->method('getPathInfo')
            ->will(self::returnValue($requestPath));

        $response = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $response->expects(self::once())
            ->method('send');

        $dispatcher = $this->getMockBuilder(Dispatcher::class)
            ->disableOriginalConstructor()
            ->getMock();

        $dispatcher->expects(self::once())
            ->method('dispatch')
            ->with(
                self::equalTo($requestMethod),
                self::equalTo($requestPath)
            )
            ->will(self::returnValue($response));

        $container = $this->getMockBuilder(Container::class)
            ->disableOriginalConstructor()
            ->getMock();

        $routeCollection = $this->getMockBuilder(RouteCollection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $routeCollection->expects(self::once())
            ->method('getDispatcher')
            ->will(self::returnValue($dispatcher));

        $restApplication = $this->getMockBuilder(RestApplication::class)
            ->disableOriginalConstructor()
            ->setMethods(['createNew', 'registerServices', 'setContainer', 'createRequest'])
            ->getMock();

        $routeCollection->expects(self::exactly(15))
            ->method('addRoute')
            ->withConsecutive(
                ['GET', '/{resource}', [$restApplication, 'callGetResources']],
                ['POST', '/{resource}', [$restApplication, 'callCreateNewResource']],
                ['PUT', '/{resource}', [$restApplication, 'callCreateNewResource']],
                ['HEAD', '/{resource}', [$restApplication, 'callMethodNotAllowed']],
                ['DEL', '/{resource}', [$restApplication, 'callMethodNotAllowed']],
                ['GET', '/{resource}/{id}', [$restApplication, 'callGetResource']],
                ['POST', '/{resource}/{id}', [$restApplication, 'callEditResource']],
                ['PUT', '/{resource}/{id}', [$restApplication, 'callEditResource']],
                ['HEAD', '/{resource}/{id}', [$restApplication, 'callResourceExists']],
                ['DEL', '/{resource}/{id}', [$restApplication, 'calldDeleteResource']],
                ['GET', '/{resource}/{id}/{child}', [$restApplication, 'callGetChildResources']],
                ['POST', '/{resource}/{id}/{child}', [$restApplication, 'callCreateNewChildResource']],
                ['PUT', '/{resource}/{id}/{child}', [$restApplication, 'callCreateNewChildResource']],
                ['HEAD', '/{resource}/{id}/{child}', [$restApplication, 'callMethodNotAllowed']],
                ['DEL', '/{resource}/{id}/{child}', [$restApplication, 'callMethodNotAllowed']]
            );

        $restApplication->expects(self::exactly(2))
            ->method('createNew')
            ->withConsecutive(
                [Container::class],
                [RouteCollection::class]
            )
            ->will(self::onConsecutiveCalls($container, $routeCollection));

        $restApplication->expects(self::once())
            ->method('registerServices')
            ->with(self::equalTo($container))
            ->will(self::returnValue($container));

        $restApplication->expects(self::once())
            ->method('setContainer')
            ->with(self::equalTo($container));

        $restApplication->expects(self::once())
            ->method('createRequest')
            ->will(self::returnValue($request));

        self::assertNull($restApplication->run());
    }

    /**
     * @test
     */
    public function getControllerSucceeds()
    {
        $controllers = [
            'test' => BaseController::class
        ];

        $controller =  $this->getMockBuilder(BaseController::class)
            ->disableOriginalConstructor()
            ->setMethods(['createNew'])
            ->getMock();

        $restApplication = $this->getMockBuilder(RestApplication::class)
            ->setConstructorArgs([$controllers])
            ->setMethods(['createNew'])
            ->getMock();

        $restApplication->expects(self::once())
            ->method('createNew')
            ->with(self::equalTo(BaseController::class))
            ->will(self::returnValue($controller));

        self::assertSame($controller, $restApplication->getController('test'));
    }
}