<?php namespace Pixel\Repositories\User;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Pixel\Contracts\User\RepositoryContract;
use Pixel\Exceptions\User\UserNotFoundException;
use Pixel\Repositories\Repository;
use Pixel\User as UserModel;

class DbRepository extends Repository implements RepositoryContract {

    use Permissions;

    /**
     * The attributes excluded from the repositories JSON form
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token', 'activation_token', 'activation_code'];

    /**
     * Create a new user
     *
     * @param array $attributes
     *
     * @return $this
     */
    public function create(array $attributes)
    {
        // Create the user and return a fresh repository instance
        $user = UserModel::create($attributes);
        $this->fill( $user->toArray() );

        return $this;
    }

    /**
     * Activate a users account
     */
    public function activate()
    {
        // Update our activation columns
        $this->setAttribute('active', 1);
        $this->hiddenAttributes[$this->getActivationTokenName()] = null;
        $this->hiddenAttributes[$this->getActivationCodeName()]  = null;

        $this->save();
    }

    /**
     * Update a users attributes
     */
    public function save()
    {
        UserModel::where('id', '=', $this->getAuthIdentifier())
            ->update($this->getAllAttributes());
    }

    /**
     * Retrieve a user by their unique identifier
     *
     * @param int $id
     *
     * @return $this
     * @throws UserNotFoundException
     */
    public function getById($id)
    {
        try {
            $user = UserModel::findOrFail($id)->toArray();
        } catch (ModelNotFoundException $e) {
            throw new UserNotFoundException($e->getMessage(), $e->getCode());
        }
        $this->fill($user);

        return $this;
    }

    /**
     * Retrieve a user by by their unique identifier and "remember me" token
     *
     * @param int     $id
     * @param string  $token
     *
     * @return $this
     * @throws UserNotFoundException
     */
    public function getByToken($id, $token)
    {
        $model = new UserModel;

        try {
            $user = $model->newQuery()
                         ->where($model->getKeyName(), $id)
                         ->where($model->getRememberTokenName(), $token)
                         ->firstOrFail()
                         ->toArray();
        } catch (ModelNotFoundException $e) {
            throw new UserNotFoundException($e->getMessage(), $e->getCode());
        }
        $this->fill($user);

        return $this;
    }

    /**
     * Retrieve a user by the given credentials
     *
     * @param array $credentials
     *
     * @return $this
     * @throws UserNotFoundException
     */
    public function getByCredentials(array $credentials)
    {
        // First we will add each credential element to the query as a where clause.
        // Then we can execute the query and, if we found a user, return it in a
        // Eloquent User "model" that will be utilized by the Guard instances.
        $model = new UserModel;
        $query = $model->newQuery();

        foreach ($credentials as $key => $value)
        {
            if ( ! str_contains($key, 'password')) $query->where($key, $value);
        }

        try {
            $this->fill( $query->firstOrFail()->toArray() );
        } catch (ModelNotFoundException $e) {
            throw new UserNotFoundException('No user with the provided credentials found');
        }

        return $this;
    }

    /**
     * Get the unique identifier for the user
     *
     * @return int
     */
    public function getAuthIdentifier()
    {
        return $this->attributes['id'];
    }

    /**
     * Get the password for the user
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->hiddenAttributes['password'];
    }

    /**
     * Get the token value for the "remember me" session
     *
     * @return string
     */
    public function getRememberToken()
    {
        return $this->hiddenAttributes[$this->getRememberTokenName()];
    }

    /**
     * Get the token value for the activation session
     *
     * @return string
     */
    public function getActivationToken()
    {
        return $this->hiddenAttributes[$this->getActivationTokenName()];
    }

    /**
     * Get the activation code for the users account
     *
     * @return string
     */
    public function getActivationCode()
    {
        return $this->hiddenAttributes[$this->getActivationCodeName()];
    }

    /**
     * Set the token value for the "remember me" session
     *
     * @param string $value
     */
    public function setRememberToken($value)
    {
        UserModel::where('id', '=', $this->getAuthIdentifier())
            ->update([$this->getRememberTokenName() => $value]);
    }

    /**
     * Set the token value for the activation session
     *
     * @param string $value
     */
    public function setActivationToken($value)
    {
        $this->hiddenAttributes[$this->getActivationTokenName()] = $value;

        UserModel::where('id', '=', $this->getAuthIdentifier())
                 ->update([$this->getActivationTokenName() => $value]);
    }

    /**
     * Make sure the supplied activation code is valid
     *
     * @param string $code
     *
     * @return bool
     */
    public function validateActivationCode($code)
    {
        // Make sure we actually have an activation code
        if ( empty($this->hiddenAttributes[$this->getActivationCodeName()]) )
            return false;

        return ($this->hiddenAttributes[$this->getActivationCodeName()] == $code);
    }

    /**
     * Make sure the supplied activation token is valid
     *
     * @param string $token
     *
     * @return bool
     */
    public function validateActivationToken($token)
    {
        // Make sure we are actually an inactive user
        if ( $this->attributes['active'] || empty($this->hiddenAttributes[$this->getActivationTokenName()]) )
            return false;

        return ($this->hiddenAttributes[$this->getActivationTokenName()] == $token);
    }

    /**
     * Get the column name for the "remember me" token
     *
     * @return string
     */
    public function getRememberTokenName()
    {
        return 'remember_token';
    }

    /**
     * Get the column name for the activation token
     *
     * @return string
     */
    public function getActivationTokenName()
    {
        return 'activation_token';
    }

    /**
     * Get the column name for the activation code
     *
     * @return string
     */
    public function getActivationCodeName()
    {
        return 'activation_code';
    }

}