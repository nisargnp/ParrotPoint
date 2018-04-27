<?php

    namespace SlideControl;

    if (!class_exists('LectureRoom')) {
        class LectureRoom {
            // map resource ID to users
            private $users;
            private $professor;
            private $connections;
            private $currPage;
            private $session_id;

            private $polling;
            private $latestPollingResults;

            private $professorName;
            private $pdfName;

            public function __construct() {
                echo "making new room\n";
                $this->professor = NULL;
                $this->users = array();
                $this->connections = array();
                $this->currPage = 1;
                $this->session_id = NULL;

                $this->polling = False;
                $this->latestPollingResults = Array(0, 0, 0, 0);
            }

            public function setSessionId($id) {
                if ($this->session_id == NULL) {
                    echo "setting session id {$id}\n";
                    $this->session_id = $id;
                }
            }

            // professor

            public function setProfessor($sid, $prof_id) {
                if ($this->professor == NULL && $this->session_id == $sid) {
                    echo "setting professor {$prof_id}\n";
                    $this->professor = $prof_id;
                }
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

            public function addUser($id, $conn, $name) {
                echo "adding user {$id}\n";
                $this->users[$id] = $name;
                $this->connections[$id] = $conn;
            }

            public function removeUser($id) {
                echo "removing user {$id}\n";
                if (isset($this->users[$id])) {
                    unset($this->users[$id]);
                    unset($this->connections[$id]);
                }
            }

            public function getTotalUsers() {
                return sizeof($this->users);
            }

            public function getConnections() {
                return array_values($this->connections);
            }

            // chat

            public function getName($id) {
                return $this->users[$id];
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

            public function setProfessorName($professorName) {
                $this->professorName = $professorName;
            }

            public function getProfessorName() {
                return $this->professorName;
            }

            public function setPDFName($pdfName) {
                $this->pdfName = $pdfName;
            }

            public function getPDFName() {
                return $this->pdfName;
            }

        }
    }


