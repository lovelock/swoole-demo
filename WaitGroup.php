<?php

class WaitGroup
{
	private $count = 0;
	private $chan;

	public function __construct()
	{
		$this->chan = new chan;
	}

	public function add()
	{
		$this->count++;
	}

	public function done()
	{
		$this->chan->push(true);
	}

	public function wait()
	{
		for ($i = 0; $i < $this->count; $i++) {
			$this->chan->pop();
		}
	}
}

go(function () {
	$wg = new WaitGroup();

	for ($i = 0; $i < 10; $i++) {
		$wg->add();
		go(function() use ($wg, $i) {
			co::sleep(2);
			echo "hello $i\n";
			$wg->done();
		});
	}

	$wg->wait();
	echo "all done\n";
});
