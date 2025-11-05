<?php

namespace App\Tests\Controller;

use App\Repository\NurseRepository;
use App\Entity\Nurse;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class NurseControllerTest extends WebTestCase
{
    /**
     * Crea un mock de Nurse con valores por defecto.
     * Ahora getPassword() devuelve un int.
     */
    private function mockNurse(array $data = []): Nurse
    {
        $nurse = $this->createMock(Nurse::class);
        $nurse->method('getId')->willReturn($data['id'] ?? 1);
        $nurse->method('getUser')->willReturn($data['usuario'] ?? 'jdoe');
        $nurse->method('getName')->willReturn($data['nombre'] ?? 'John Doe');
        $nurse->method('getEmail')->willReturn($data['email'] ?? 'jdoe@example.com');
        // password es int ahora
        $nurse->method('getPassword')->willReturn($data['password'] ?? 1234);
        $nurse->method('isWorking')->willReturn($data['working'] ?? false);
        return $nurse;
    }

    private function replaceRepoMock($mock)
    {
        static::getContainer()->set(NurseRepository::class, $mock);
    }

    public function testLoginMissingFields()
    {
        $client = static::createClient();
        $client->request('POST', '/nurse/login', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([]));

        $this->assertSame(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
        $resp = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame('usuario y password obligatorios', $resp['message']);
    }

    public function testLoginNotFound()
    {
        $client = static::createClient();

        $repo = $this->createMock(NurseRepository::class);
        // password como int en el mock esperado
        $repo->expects($this->once())
            ->method('login')
            ->with('user1', 9999)
            ->willReturn(null);

        $this->replaceRepoMock($repo);

        $client->request('POST', '/nurse/login', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'usuario' => 'user1',
            'password' => 9999, // número, no string
        ]));

        $this->assertSame(Response::HTTP_NOT_FOUND, $client->getResponse()->getStatusCode());
        $resp = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame('No encontrado o credenciales incorrectas', $resp['message']);
    }

    public function testLoginSuccess()
    {
        $client = static::createClient();

        $nurse = $this->mockNurse(['id' => 42, 'usuario' => 'user1', 'nombre' => 'Nombre', 'password' => 5555]);
        $repo = $this->createMock(NurseRepository::class);
        $repo->expects($this->once())
            ->method('login')
            ->with('user1', 5555)
            ->willReturn($nurse);

        $this->replaceRepoMock($repo);

        $client->request('POST', '/nurse/login', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'usuario' => 'user1',
            'password' => 5555,
        ]));

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $resp = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame(42, $resp['id']);
        $this->assertSame('user1', $resp['usuario']);
        $this->assertSame('Nombre', $resp['nombre']);
    }

    public function testGetAll()
    {
        $client = static::createClient();

        $n1 = $this->mockNurse(['id' => 1, 'usuario' => 'u1', 'nombre' => 'A']);
        $n2 = $this->mockNurse(['id' => 2, 'usuario' => 'u2', 'nombre' => 'B']);

        $repo = $this->createMock(NurseRepository::class);
        $repo->expects($this->once())
            ->method('getAll')
            ->willReturn([$n1, $n2]);

        $this->replaceRepoMock($repo);

        $client->request('GET', '/nurse/index');

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $resp = json_decode($client->getResponse()->getContent(), true);
        $this->assertCount(2, $resp);
        $this->assertSame('u1', $resp[0]['usuario']);
        $this->assertSame('u2', $resp[1]['usuario']);
    }

    public function testFindByUserNotFound()
    {
        $client = static::createClient();

        $repo = $this->createMock(NurseRepository::class);
        $repo->expects($this->once())
            ->method('findByUser')
            ->with('noexist')
            ->willReturn(null);

        $this->replaceRepoMock($repo);

        $client->request('GET', '/nurse/user/noexist');

        $this->assertSame(Response::HTTP_NOT_FOUND, $client->getResponse()->getStatusCode());
        $resp = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame('No encontrado', $resp['message']);
    }

    public function testFindByUserSuccess()
    {
        $client = static::createClient();

        $nurse = $this->mockNurse(['id' => 7, 'usuario' => 'u7', 'nombre' => 'N7']);
        $repo = $this->createMock(NurseRepository::class);
        $repo->expects($this->once())
            ->method('findByUser')
            ->with('u7')
            ->willReturn($nurse);

        $this->replaceRepoMock($repo);

        $client->request('GET', '/nurse/user/u7');

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $resp = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame(7, $resp['id']);
        $this->assertSame('u7', $resp['usuario']);
        $this->assertSame('N7', $resp['nombre']);
    }

    public function testRegisterMissingFields()
    {
        $client = static::createClient();
        $repo = $this->createMock(NurseRepository::class);
        $repo->expects($this->never())->method('create'); // no debe llamarse
        $this->replaceRepoMock($repo);

        $client->request('POST', '/nurse/register', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'name' => 'Apenas', // faltan usuario, password, email
        ]));

        $this->assertSame(
            Response::HTTP_BAD_REQUEST,
            $client->getResponse()->getStatusCode(),
            $client->getResponse()->getContent() // <-- ayuda mostrando la excepción
        );
        $resp = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame('Faltan campos obligatorios', $resp['message']);
    }

    public function testRegisterUserExists()
    {
        $client = static::createClient();

        $repo = $this->createMock(NurseRepository::class);
        $repo->expects($this->once())
            ->method('findByUser')
            ->with('uexists')
            ->willReturn($this->mockNurse(['usuario' => 'uexists']));

        $repo->expects($this->never())->method('create');

        $this->replaceRepoMock($repo);

        $client->request('POST', '/nurse/register', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'name' => 'T',
            'usuario' => 'uexists',
            'password' => 1111,
            'email' => 'e@e.com',
        ]));

        $this->assertSame(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
        $resp = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame('El usuario ya existe', $resp['message']);
    }

    public function testRegisterSuccess()
    {
        $client = static::createClient();

        $repo = $this->createMock(NurseRepository::class);
        $repo->expects($this->once())->method('findByUser')->willReturn(null);
        $repo->expects($this->once())->method('create')->with($this->isInstanceOf(Nurse::class));

        $this->replaceRepoMock($repo);

        $payload = [
            'name' => 'New',
            'usuario' => 'newuser',
            'password' => 2222, // int
            'email' => 'new@example.com',
            'working' => true,
        ];

        $client->request('POST', '/nurse/register', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($payload));

        $this->assertSame(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());
        $resp = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame('Nurse creada correctamente', $resp['message']);
    }

    public function testDeleteNotFound()
    {
        $client = static::createClient();

        $repo = $this->createMock(NurseRepository::class);
        $repo->expects($this->once())
            ->method('findByUser')
            ->with('ghost')
            ->willReturn(null);

        $this->replaceRepoMock($repo);

        $client->request('DELETE', '/nurse/delete', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'usuario' => 'ghost',
        ]));

        $this->assertSame(Response::HTTP_NOT_FOUND, $client->getResponse()->getStatusCode());
        $resp = json_decode($client->getResponse()->getContent(), true);
        $this->assertStringContainsString('no encontrado', $resp['message']);
    }

    public function testDeleteSuccess()
    {
        $client = static::createClient();

        $nurse = $this->mockNurse(['nombre' => 'ToDelete', 'usuario' => 'todel']);
        $repo = $this->createMock(NurseRepository::class);
        $repo->expects($this->once())
            ->method('findByUser')
            ->with('todel')
            ->willReturn($nurse);
        $repo->expects($this->once())->method('delete')->with($nurse);

        $this->replaceRepoMock($repo);

        $client->request('DELETE', '/nurse/delete', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'usuario' => 'todel',
        ]));

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $resp = json_decode($client->getResponse()->getContent(), true);
        $this->assertStringContainsString('Se ha eliminado correctamente', $resp['message']);
    }

    public function testEditNotFound()
    {
        $client = static::createClient();

        $repo = $this->createMock(NurseRepository::class);
        $repo->expects($this->once())
            ->method('findByUser')
            ->with('noedit')
            ->willReturn(null);

        $this->replaceRepoMock($repo);

        $client->request('PUT', '/nurse/edit/noedit', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'name' => 'X'
        ]));

        $this->assertSame(Response::HTTP_NOT_FOUND, $client->getResponse()->getStatusCode());
        $resp = json_decode($client->getResponse()->getContent(), true);
        $this->assertStringContainsString('no encontrado', $resp['message']);
    }

    public function testEditSuccess()
    {
        $client = static::createClient();

        $nurse = $this->mockNurse(['usuario' => 'editu', 'nombre' => 'OldName']);
        $repo = $this->createMock(NurseRepository::class);
        $repo->expects($this->once())
            ->method('findByUser')
            ->with('editu')
            ->willReturn($nurse);
        $repo->expects($this->once())->method('edit')->with($nurse);

        $this->replaceRepoMock($repo);

        $client->request('PUT', '/nurse/edit/editu', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'name' => 'NewName'
        ]));

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $resp = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame('Nurse actualizado correctamente', $resp['message']);
    }
}
