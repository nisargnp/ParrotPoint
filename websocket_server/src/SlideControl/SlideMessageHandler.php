<?php

	namespace SlideControl;
	use Ratchet\MessageComponentInterface;
	use Ratchet\ConnectionInterface;
	require_once "LectureRoom.php";

	class SlideMessageHandler implements MessageComponentInterface {
		protected $clients;

		private $room_list, $user_list;

		public function __construct() {
			$this->clients = new \SplObjectStorage;
			$this->room_list = array();
			$this->user_list = array();
		}

		public function onOpen(ConnectionInterface $conn) {
			// Store the new connection to send messages to later
			$this->clients->attach($conn);

			echo "New connection! ({$conn->resourceId})\n";

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

			if ($header == "join") {
				if (!array_key_exists($from->resourceId, $this->users_list)) {
					$spl = explode(":", $payload, 2);
					$c = $spl[0];
					echo "joining room {$c}\n";

					if (array_key_exists($c, $this->room_list)) {
						$this->room_list[$c]->addUser($from->resourceId, $from, $spl[1]);
						$this->users_list[$from->resourceId] = $c;

						// send current page number for initial rendering
						$from->send($this->room_list[$c]->getPage());

						// send chat welcome message
						$from->send("chat:Professor:Welcome to the chat!");

						// send polling status
						if ($this->room_list[$c]->currentlyPolling()) {
							$from->send("polling:active");
						}
					}
				}
			}
			else if ($header == "make-room") {
				$spl = explode(":", $payload, 2);
				$c = $spl[0];
				echo "new room with code {$c}\n";
				$this->room_list[$c] = new LectureRoom;
				$this->room_list[$c]->setSessionId($spl[1]);

			}
			else if (array_key_exists($from->resourceId, $this->users_list)) {
				$code = $this->users_list[$from->resourceId];

				if ($header == "chat") {
					foreach ($this->room_list[$code]->getConnections() as $client) {
						if ($from !== $client) {
							// The sender is not the receiver, send to each client connected
							$uname = $this->room_list[$code]->getName($from->resourceId);

							$client->send("chat:" . $uname . ":" . $payload);
						}
					}
				}
				else if ($header == "auth-professor") {
					$this->room_list[$code]->setProfessor($payload, $from->resourceId);
				}

				// from professor client -> sends this msg to start polling
				else if ($header == "polling-start" && $this->room_list[$code]->isProfessor($from->resourceId)) {
					echo "polling-start\n";
					$numAnswers = 4;
					$this->room_list[$code]->startPolling();
					foreach ($this->room_list[$code]->getConnections() as $conn) {
						$conn->send("polling:start:$numAnswers");
					}
				}

				// from professor client -> sends this msg to stop polling
				else if ($header == "polling-stop" && $this->room_list[$code]->isProfessor($from->resourceId)) {
					echo "polling-stop\n";
					$this->room_list[$code]->stopPolling();
					foreach ($this->room_list[$code]->getConnections() as $conn) {
						$conn->send("polling:stop");
						$polling_results = $this->room_list[$code]->getResults();

						// todo: remove hardcode
						//$polling_results = Array(1,12,7,3);

						$conn->send("polling:results:" . json_encode($polling_results));
					}
				}

				// from student client -> polling reply
				else if ($header == "polling-reply") {
					echo "polling-reply\n";
					$data = json_decode($payload);
					$this->room_list[$code]->updateResults($data);
				}

				else {
					if (is_numeric($msg)) {
						if ($this->room_list[$code]->isProfessor($from->resourceId)) {
								
							$this->room_list[$code]->setPage(intval($msg));

							$cp = $this->room_list[$code]->getPage();
							foreach ($this->room_list[$code]->getConnections() as $client) {
								if ($from !== $client) {
									// The sender is not the receiver, send to each client connected
									$client->send($cp);
								}
							}
						}
					}
				}
			}
		}

		public function onClose(ConnectionInterface $conn) {
			// The connection is closed, remove it, as we can no longer send it messages
			$this->clients->detach($conn);

			if (array_key_exists($conn->resourceId, $this->users_list)) {
				$code = $this->users_list[$conn->resourceId];
				$this->room_list[$code]->removeUser($conn->resourceId);	

				unset($this->users_list[$conn->resourceId]);
			}

			echo "Connection {$conn->resourceId} has disconnected\n";
		}

		public function onError(ConnectionInterface $conn, \Exception $e) {
			echo "An error has occurred: {$e->getMessage()}\n";

			$conn->close();
		}

	}
