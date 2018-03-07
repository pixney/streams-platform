<?php namespace Anomaly\Streams\Platform\Version;

use Anomaly\Streams\Platform\Entry\Contract\EntryInterface;
use Anomaly\Streams\Platform\Model\EloquentModel;
use Anomaly\Streams\Platform\Support\Presenter;
use Anomaly\Streams\Platform\Version\Contract\VersionInterface;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Robbo\Presenter\PresentableInterface;

/**
 * Class VersionModel
 *
 * @link   http://pyrocms.com/
 * @author PyroCMS, Inc. <support@pyrocms.com>
 * @author Ryan Thompson <ryan@pyrocms.com>
 */
class VersionModel extends Model implements VersionInterface, PresentableInterface
{

    /**
     * Eager load these relations.
     *
     * @var array
     */
    protected $with = [
        'createdBy',
    ];

    /**
     * The timestamps flag.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The model table.
     *
     * @var string
     */
    protected $table = 'versions';

    /**
     * The primary key.
     *
     * @var string
     */
    public $primaryKey = 'version';

    /**
     * Set the versionable attributes.
     *
     * @param EloquentModel $value
     */
    public function setVersionableAttribute(EloquentModel $value)
    {
        $this->attributes['versionable_id']   = $value->getId();
        $this->attributes['versionable_type'] = get_class($value);
    }

    /**
     * Get the created at attribute.
     *
     * @return Carbon
     */
    public function getCreatedAtAttribute()
    {
        return new Carbon($this->attributes['created_at']);
    }

    /**
     * Get the data attribute.
     *
     * @return array
     */
    public function getDataAttribute()
    {
        return unserialize($this->attributes['data']);
    }

    /**
     * Return a created presenter.
     *
     * @return Presenter
     */
    public function getPresenter()
    {
        return new VersionPresenter($this);
    }

    /**
     * Get the version.
     *
     * @return int
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Get the related versionable.
     *
     * @return EntryInterface|EloquentModel
     */
    public function getVersionable()
    {
        return $this->versionable;
    }

    /**
     * Return the versionable relation.
     *
     * @return MorphTo
     */
    public function versionable()
    {
        return $this->morphTo('versionable');
    }

    /**
     * Return the related creator.
     *
     * @return Authenticatable
     */
    public function getCreatedBy()
    {
        return $this
            ->createdBy()
            ->getResults();
    }

    /**
     * Return the creator relation.
     *
     * @return BelongsTo
     */
    public function createdBy()
    {
        return $this->belongsTo(config('auth.providers.users.model'));
    }

}
