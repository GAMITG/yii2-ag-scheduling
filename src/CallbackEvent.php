<?php

namespace gamitg\scheduling;
use Yii;
use yii\base\Application;
use yii\base\InvalidArgumentException;
use yii\mutex\Mutex;

/**
 * Class CallbackEvent
 * @author <Pavel Agalecky:pavel.agalecky@gmail.com>
 * @modifiedBy <GAMITG>
 */
class CallbackEvent extends Event
{
    /**
     * The callback to call.
     *
     * @var string
     */
    protected $callback;
    /**
     * The parameters to pass to the method.
     *
     * @var array
     */
    protected $parameters;

    /**
     * Create a new event instance.
     *
     * @param Mutex $mutex
     * @param string $callback
     * @param array $parameters
     * @param array $config
     * @throws InvalidArgumentException
     */
    public function __construct(Mutex $mutex, $callback, array $parameters = [], $config = [])
    {
        $this->callback = $callback;
        $this->parameters = $parameters;
        $this->_mutex = $mutex;

        if (!empty($config)) {
            Yii::configure($this, $config);
        }

        if ( ! is_string($this->callback) && ! is_callable($this->callback)) {
            throw new InvalidArgumentException(
                "Invalid scheduled callback event. Must be string or callable."
            );
        }
    }

    /**
     * Run the given event.
     *
     * @param Application $app
     * @return mixed
     */
    public function run(Application $app)
    {
        $this->trigger(self::EVENT_BEFORE_RUN);
        $response = call_user_func_array($this->callback, array_merge($this->parameters, [$app]));
        parent::callAfterCallbacks($app);
        $this->trigger(self::EVENT_AFTER_RUN);
        return $response;
    }

    /**
     * Do not allow the event to overlap each other.
     *
     * @return $this
     * @throws InvalidArgumentException
     */
    public function withoutOverlapping()
    {
        if (empty($this->_description)) {
            throw new InvalidArgumentException(
                "A scheduled event name is required to prevent overlapping. Use the 'description' method before 'withoutOverlapping'."
            );
        }
        return parent::withoutOverlapping();
    }

    /**
     * Get the mutex name for the scheduled command.
     *
     * @return string
     */
    protected function mutexName(): string
    {
        return 'framework/schedule-' . sha1($this->_description);
    }

    /**
     * Get the summary of the event for display.
     *
     * @return string
     */
    public function getSummaryForDisplay(): string
    {
        if (is_string($this->_description)) return $this->_description;
        return is_string($this->callback) ? $this->callback : 'Closure';
    }
}