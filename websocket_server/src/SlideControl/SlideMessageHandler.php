<?php

	namespace SlideControl;
	use Ratchet\MessageComponentInterface;
	use Ratchet\ConnectionInterface;

	class SlideMessageHandler implements MessageComponentInterface {
		protected $clients;
		private $curr_page;

		public function __construct() {
			$this->clients = new \SplObjectStorage;
			$this->curr_page = 1;
		}

		public function onOpen(ConnectionInterface $conn) {
			// Store the new connection to send messages to later
			$this->clients->attach($conn);

			echo "New connection! ({$conn->resourceId})\n";

			// send current page number for initial rendering
			$conn->send($this->curr_page);
		}

		public function onMessage(ConnectionInterface $from, $msg) {
			$numRecv = count($this->clients) - 1;
			echo sprintf('Connection %d sending message "%s" to %d other connection%s' . "\n"
				, $from->resourceId, $msg, $numRecv, $numRecv == 1 ? '' : 's');

			$chunks = explode(":", $msg, 2);
			$header = $chunks[0];
			$payload = $chunks[1];

			if ($header == "chat") {
				foreach ($this->clients as $client) {
					if ($from !== $client) {
						// The sender is not the receiver, send to each client connected
						$client->send("chat:" . $from->resourceId . ":" . $payload);
					}
				}
			}
			else {

				if (is_numeric($msg)) {
					$this->curr_page = intval($msg);
				}

				foreach ($this->clients as $client) {
					if ($from !== $client) {
						// The sender is not the receiver, send to each client connected
						$client->send($this->curr_page);
					}
				}
			}
		}

		public function onClose(ConnectionInterface $conn) {
			// The connection is closed, remove it, as we can no longer send it messages
			$this->clients->detach($conn);

			echo "Connection {$conn->resourceId} has disconnected\n";
		}

		public function onError(ConnectionInterface $conn, \Exception $e) {
			echo "An error has occurred: {$e->getMessage()}\n";

			$conn->close();
		}
	}
