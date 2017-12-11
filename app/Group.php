<?php

namespace KBox;

use Franzose\ClosureTable\Models\Entity;
use Illuminate\Database\Eloquent\SoftDeletes;
use KBox\Traits\LocalizableDateFields;

/**
 * A collection of document descriptors
 *
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $deleted_at
 * @property string $color
 * @property string $is_private
 * @property int $group_type_id
 * @property int $parent_id
 * @property int $position
 * @property int $real_depth
 * @property-read \Illuminate\Database\Eloquent\Collection|\KBox\DocumentDescriptor[] $documents
 * @property-read \KBox\Project $project
 * @property-read \Illuminate\Database\Eloquent\Collection|\KBox\Shared[] $shares
 * @property-read \KBox\User $user
 * @method static \Illuminate\Database\Query\Builder|\KBox\Group byName($name)
 * @method static \Illuminate\Database\Query\Builder|\KBox\Group ofType($type)
 * @method static \Illuminate\Database\Query\Builder|\KBox\Group orPrivate($user_id)
 * @method static \Illuminate\Database\Query\Builder|\KBox\Group orPublic()
 * @method static \Illuminate\Database\Query\Builder|\KBox\Group private($user_id)
 * @method static \Illuminate\Database\Query\Builder|\KBox\Group public()
 * @method static \Illuminate\Database\Query\Builder|\KBox\Group roots()
 * @method static \Illuminate\Database\Query\Builder|\KBox\Group whereColor($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\Group whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\Group whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\Group whereGroupTypeId($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\Group whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\Group whereIsPrivate($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\Group whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\Group whereParentId($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\Group wherePosition($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\Group whereRealDepth($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\Group whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\Group whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\Group withAllDescendants()
 * @mixin \Eloquent
 */
class Group extends Entity implements GroupInterface
{

    // https://github.com/franzose/ClosureTable

    /*
    id: bigIncrements
    name: string
    color: string (hex color)
    created_at: date
    updated_at: date
    deleted_at: date
    user_id: User
    group_type_id: GroupType
    parent_id: Group
    is_private:boolean (default true)
    */

    use SoftDeletes, LocalizableDateFields;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'groups';

    /**
     * ClosureTable model instance.
     *
     * @var groupClosure
     */
    protected $closure = 'KBox\GroupClosure';

    
    protected $fillable = ['name','color', 'user_id','parent_id', 'group_type_id', 'is_private'];

    public $timestamps = true;

    public function user()
    {
        
        // One to One
        return $this->belongsTo('KBox\User')->withTrashed();
    }
    
    public function project()
    {
        return $this->belongsTo('KBox\Project', 'id', 'collection_id');
    }

    /**
     * [documents description]
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany [description]
     */
    public function documents()
    {
        return $this->belongsToMany('KBox\DocumentDescriptor', 'document_groups', 'group_id', 'document_id')->local();
    }

    /**
     * Get this group plus all descendants query
     */
    public function scopeWithAllDescendants()
    {
        return $this->joinClosureBy('descendant', true);
    }

    public function shares()
    {
        return $this->morphMany('KBox\Shared', 'shareable');
    }

    public function scopeOfType($query, $type)
    {
        return $query->whereType($type);
    }

    public function scopeByName($query, $name)
    {
        return $query->whereName($name);
    }

    public function scopePublic($query)
    {
        return $query->where('is_private', false);
    }
    
    public function scopeOrPublic($query)
    {
        return $query->orWhere('is_private', false);
    }

    public function scopePrivate($query, $user_id)
    {
        return $query->where(function ($query) use ($user_id) {
            $query->where('is_private', true)
                      ->where('user_id', $user_id);
        });
    }
    
    public function scopeOrPrivate($query, $user_id)
    {
        return $query->orWhere(function ($query) use ($user_id) {
            $query->where('is_private', true)
                      ->where('user_id', $user_id);
        });
    }

    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }

    
    public static function getPersonalTree($user_id, array $columns = ['*'])
    {
        /**
         * @var Entity $instance
         */
        $instance = new static;
        $columns = $instance->prepareTreeQueryColumns($columns);

        
        return $instance
            ->where('is_private', '=', true)
            ->where('user_id', '=', $user_id)
            ->orderBy('name', 'asc')
            ->get($columns)->toTree();
    }

    public static function getProjectsTree(array $columns = ['*'])
    {
        /**
         * @var Entity $instance
         */
        $instance = new static;
        $columns = $instance->prepareTreeQueryColumns($columns);

        
        return $instance
            ->where('is_private', '=', false)
            ->orderBy('name', 'asc')
            ->get($columns)->toTree();
    }

    /**
     * Convert the group to the K-Search collection representation
     *
     * @return string|boolean the id of the group, false if is trashed
     */
    public function toKlinkGroup()
    {
        if ($this->trashed()) {
            return false;
        }

        return $this->id;
    }
    
    /**
     * Overcome the problem that is_private is stored as a string because it is in a key
     */
    public function getIsPrivateAttribute($value)
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
    
    public function isSharedWith($user)
    {
        return $this->shares()->sharedWithMe($user)->count() > 0;
    }

    public function getNameAttribute($value)
    {

        // some values can be escaped, like the single quote char ' to #039; and needs to be escaped
        return htmlspecialchars_decode($value, ENT_QUOTES);
    }
    
    
    public static function getClosureTable()
    {
        $instance = new static;
        return $instance->closure;
    }
    
    public static function boot()
    {
        parent::boot();

        static::created(function ($group) {
            if ($group->is_private) {
                \Cache::forget('dms_personal_collections'.$group->user_id);
            } else {
                \Cache::forget('dms_project_collections-'.$group->user_id);
            }
            
            return $group;
        });
        
        static::updated(function ($group) {
            if ($group->is_private) {
                \Cache::forget('dms_personal_collections'.$group->user_id);
            } else {
                \Cache::forget('dms_project_collections-'.$group->user_id);
            }
            
            return $group;
        });
    }
}
