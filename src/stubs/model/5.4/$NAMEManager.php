<?php

namespace Core\User;

use Core\User\Exceptions\MissingParamException;
use Core\User\Exceptions\UsernameAlreadyUsedException;
use Core\User\Exceptions\EmailAlreadyUsedException;
use Core\User\Exceptions\EmailInvalidException;
use Core\User\Exceptions\WeakPasswordException;
use Core\User\Exceptions\MismatchRepeatPasswordException;

use Core\Manager\Exceptions\InvalidValueParamException;

use Core\User\User;
use Core\User\UserRepository;

use Core\Manager\Manager;
use Core\Manager\ManagerEntityContract;

class UserManager extends Manager
{

	/**
	 * Entity class
	 *
	 * @var string
	 */
	protected $entity = User::class;

    /**
	 * Repository
	 *
     * @var UserRepository
     */
    protected $repository;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->repository = new UserRepository();
    }

    /**
     * Retrieve repository
     *
     * @return Repository
     */
    public function getRepository()
    {
        return $this->repository;
    }

	/**
     * Throw an exception if a parameter is null
     *
     * @param array $params
     *
     * @return void
     */
    public function throwExceptionParamsNull($params)
    {
        foreach($params as $name => $value) {
            if($value == null) {
                throw new MissingParamException("Missing parameter: {$name}");
            }
        }
    }

    /**
     * Throw an exception if a parameter is missing
     *
     * @param array $required
     * @param array $sent
     *
     * @return void
     */
    public function throwExceptionMissingParam($required, $sent)
    {
        foreach($required as $param) {
            if(!isset($sent[$param])) {
                throw new MissingParamException("Missing parameter: {$param}");
            }
        }
    }

    /**
     * Throw an exception if email already exists
     *
     * @param string $email
     *
     * @return void
     */
    public function throwExceptionEmailAlreadyUsed(ManagerEntityContract $entity, $email)
    {
        if($this->getRepository()->uniqueByEmail($entity, $email))
            throw new EmailAlreadyUsedException();
    }

    /**
     * Throw an exception if username already exists
     *
     * @param string $username
     *
     * @return void
     */
    public function throwExceptionUsernameAlreadyUsed(ManagerEntityContract $entity, $username)
    {
        if($this->getRepository()->uniqueByUsername($entity, $username))
            throw new UsernameAlreadyUsedException();
    }

    /**
     * Throw an exception if email is invalid
     *
     * @param string $email
     *
     * @return void
     */
    public function throwExceptionEmailInvalid($email)
    {
        //throw new EmailInvalidException();
    }

    /**
     * Throw an exception if password is weak
     *
     * @param string $password
     *
     * @return void
     */
    public function throwExceptionPasswordTooWeak($password)
    {

        if(strlen($password) < 3)
            throw new PasswordTooWeakException();
    }

    /**
     * Throw an exception if password is weak
     *
     * @param string $password
     * @param string $password_repeat
     *
     * @return void
     */
    public function throwExceptionMismatchRepeatPassword($password, $password_repeat)
    {
        if($password !== $password_repeat)
            throw new MismatchRepeatPasswordException();
    }

	/**
	 * Fill the entity
	 *
	 * @param ManagerEntityContract $entity
	 * @param array $params
	 *
	 * @return ManagerEntityContract
	 */
	public function fill(ManagerEntityContract $user, array $params)
	{

		$params = array_intersect_key($params, array_flip(['username', 'email', 'password', 'password_repeat', 'role']));


		// $this->throwExceptionMissingParam(['username', 'password', 'password_repeat', 'email'], $params);

		if (isset($params['username'])) {
			$this->throwExceptionUsernameAlreadyUsed($user, $params['username']);
		}

		if (isset($params['email'])) {
			$this->throwExceptionEmailAlreadyUsed($user, $params['email']);
			$this->throwExceptionEmailInvalid($params['email']);
		}

		if (isset($params['password']) && isset($params['password_repeat'])) {
			$this->throwExceptionMismatchRepeatPassword($params['password'], $params['password_repeat']);
		}

		if (isset($params['password'])) {
			$this->throwExceptionPasswordTooWeak($params['password']);
			$params['password'] = bcrypt($params['password']);
		}

		if (isset($params['role'])) {
			$this->throwExceptionInvalidParamValue('role', $params['role'], ['user', 'admin']);
		}



		$user->fill($params);

		// Temporary ?
		if (isset($params['username'])) {
			$user->name=$user->username;
		}

		return $user;

	}

	/**
	 * This will prevent from saving entity with null value
	 *
	 * @param ManagerEntityContract $entity
	 *
	 * @return ManagerEntityContract
	 */
	public function save(ManagerEntityContract $entity)
	{
		$this->throwExceptionParamsNull([
			'username' => $entity->username,
			'password' => $entity->password,
			'email' => $entity->email,
			'role' => $entity->role
		]);

		return parent::save($entity);
	}

	/**
	 * To array
	 *
	 * @param Core\Manager\ManagerEntityContract $entity
	 *
	 * @return array
	 */
	public function toArray(ManagerEntityContract $entity)
	{
		return [];
	}
}
