<?php

	namespace SlideControl;
	use Ratchet\MessageComponentInterface;
	use Ratchet\ConnectionInterface;
	require_once "LectureRoom.php";

	class SlideMessageHandler implements MessageComponentInterface {
		protected $clients;

		private $one_room_for_now;

		public function __construct() {
			$this->clients = new \SplObjectStorage;
			$this->one_room_for_now = new LectureRoom;
		}

		public function onOpen(ConnectionInterface $conn) {
			// Store the new connection to send messages to later
			$this->clients->attach($conn);

			echo "New connection! ({$conn->resourceId})\n";

			// add to master room for now
			// future needs to determine which room to put them in somehow
			$this->one_room_for_now->addUser($conn->resourceId, $conn);

			// send current page number for initial rendering
			$conn->send($this->one_room_for_now->getPage());

			// send chat welcome message
			$conn->send("chat:Professor:Welcome to the chat!");

			// send polling status
			if ($this->one_room_for_now->currentlyPolling()) {
				$conn->send("polling:active");
			}

		}

		public function onMessage(ConnectionInterface $from, $msg) {
			$numRecv = count($this->clients) - 1;
			echo sprintf('Connection %d sending message "%s" to %d other connection%s' . "\n"
				, $from->resourceId, $msg, $numRecv, $numRecv == 1 ? '' : 's');

			// get message type
			$chunks = explode(":", $msg, 2);
			$header = $chunks[0];
			# I don't have PHP7...
			$payload = array_key_exists(1, $chunks) ? $chunks[1] : "";

			print_r($chunks);

			if ($header == "chat") {
				foreach ($this->one_room_for_now->getConnections() as $client) {
					if ($from !== $client) {
						// The sender is not the receiver, send to each client connected
						$uname = $this->one_room_for_now->getName($from->resourceId);

						$client->send("chat:" . $uname . ":" . $payload);
					}
				}
			}
			else if ($header == "set-name") {
				$this->one_room_for_now->setName($from->resourceId, $payload);
			}
			else if ($header == "auth-professor") {
				$this->one_room_for_now->setProfessor($from->resourceId);
			}

			// from professor client -> sends this msg to start polling
			else if ($header == "polling-start" /*&& $this->one_room_for_now->isProfessor($from->resourceId)*/) {
				echo "polling-start\n";
				$numAnswers = 4;
				$this->one_room_for_now->startPolling();
				foreach ($this->one_room_for_now->getConnections() as $conn) {
					$conn->send("polling:start:$numAnswers");
				}
			}

			// from professor client -> sends this msg to stop polling
			else if ($header == "polling-stop" /*&& $this->one_room_for_now->isProfessor($from->resourceId)*/) {
				echo "polling-stop\n";
				$this->one_room_for_now->stopPolling();
				foreach ($this->one_room_for_now->getConnections() as $conn) {
					$conn->send("polling:stop");
					$polling_results = $this->one_room_for_now->getResults();

					// todo: remove hardcode
					//$polling_results = Array(1,12,7,3);

					$conn->send("polling:results:" . json_encode($polling_results));
				}
			}

			// from student client -> polling reply
			else if ($header == "polling-reply") {
				echo "polling-reply\n";
				$data = json_decode($payload);
				$this->one_room_for_now->updateResults($data);
			}

			else {
				if ($this->one_room_for_now->isProfessor($from->resourceId)) {
					if (is_numeric($msg)) {
						$this->one_room_for_now->setPage(intval($msg));
					}

					foreach ($this->one_room_for_now->getConnections() as $client) {
						if ($from !== $client) {
							// The sender is not the receiver, send to each client connected
							$client->send($this->one_room_for_now->getPage());
						}
					}
				}
			}
		}

		public function onClose(ConnectionInterface $conn) {
			// The connection is closed, remove it, as we can no longer send it messages
			$this->clients->detach($conn);

			$this->one_room_for_now->removeUser($conn->resourceId);			

			echo "Connection {$conn->resourceId} has disconnected\n";
		}

		public function onError(ConnectionInterface $conn, \Exception $e) {
			echo "An error has occurred: {$e->getMessage()}\n";

			$conn->close();
		}

	}
