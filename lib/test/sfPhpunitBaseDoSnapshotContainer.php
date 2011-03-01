<?php

abstract class sfPhpunitBaseDoSnapshotContainer
{
  /**
   *
   * @var sfEventDispatcher
   */
  protected $_dispather;

  /**
   *
   * @var sfFormatter
   */
  protected $_formatter;

  public function __construct(sfEventDispatcher $dispatcher, sfFormatter $formatter)
  {
    $this->_dispather = $dispatcher;
    $this->_formatter = $formatter;
  }

  abstract public function doSnapshots();

  // COPY PASTE FROM sfTask

  /**
   * Logs a message.
   *
   * @param mixed $messages  The message as an array of lines of a single string
   */
  public function log($messages)
  {
    if (!is_array($messages))
    {
      $messages = array($messages);
    }

    $this->dispatcher->notify(new sfEvent($this, 'command.log', $messages));
  }
}