<?php namespace Pixel;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract {

	use Authenticatable, CanResetPassword;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['name', 'email', 'password'];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = ['password', 'remember_token'];

	/**
	 * The attributes that should be casted to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'id'           => 'integer',
		'is_admin'     => 'boolean',
		'is_moderator' => 'boolean',
	];

    /**
     * Image relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function images()
    {
        return $this->hasMany('Image');
    }

	/**
	 * Is this user an administrator? (Referenced as a function for future proofing)
	 *
	 * @return boolean
	 */
	public function isAdmin() { return $this->getAttributeValue('is_admin'); }

	/**
	 * Is this user a moderator? (Referenced as a function for future proofing)
	 *
	 * @return boolean
	 */
	public function isModerator() { return $this->getAttributeValue('is_moderator'); }

}
