<?php

    namespace SlideControl;

    if (!class_exists('LectureRoom')) {
        class LectureRoom {
            // map resource ID to users
            private $users;
            private $professor;
            private $connections;
            private $currPage;

            private $polling;
            private $latestPollingResults;

            public function __construct() {
                $this->professor = NULL;
                $this->users = array();
                $this->connections = array();
                $this->currPage = 1;

                $this->polling = False;
                $this->latestPollingResults = Array(0, 0, 0, 0);
            }

            // professor

            public function setProfessor($prof_id) {
                echo "setting professor {$prof_id}\n";
                $this->professor = $prof_id;
            }

            public function isProfessor($id) {
                echo "checking professor {$id}\n";
                if ($this->professor == $id) {
                    return true;
                }
                else {
                    return false;
                }
            }

            // users

            public function addUser($id, $conn) {
                echo "adding user {$id}\n";
                $this->users[$id] = $id;
                $this->connections[$id] = $conn;
            }

            public function removeUser($id) {
                echo "removing user {$id}\n";
                if (isset($this->users[$id])) {
                    unset($this->users[$id]);
                    unset($this->connections[$id]);
                }
            }

            public function getConnections() {
                return array_values($this->connections);
            }

            // chat

            public function getName($id) {
                return $this->users[$id];
            }

            public function setName($id, $name) {
                echo "setting name user {$id} to {$name}\n";
                if ($this->users[$id] == $id) {
                    $this->users[$id] = $name;
                }
                else {
                    $bname = $this->users[$id];
                    echo "bad: {$bname}\n";
                }
            }

            // lecture pages

            public function getPage() {
                echo "getting page\n";
                return $this->currPage;
            }

            public function setPage($n) {
                echo "setting new page {$n}\n";
                $this->currPage = $n;
            }

            public function startPolling() {
                $this->polling = True;
                $this->latestPollingResults = Array(0, 0, 0, 0);
            }

            public function stopPolling() {
                $this->polling = false;
            }

            public function currentlyPolling() {
                return $this->polling;
            }

            public function updateResults(array $updates) {
                print_r($this->latestPollingResults);
                foreach ($updates as $index=>$update) {
                    $this->latestPollingResults[$index] += $update;
                }
            }

            public function getResults() {
                return $this->latestPollingResults;
            }

        }
    }


