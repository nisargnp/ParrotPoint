<?php

    namespace SlideControl;

    if (!class_exists('LectureRoom')) {
        class LectureRoom {
            // map resource ID to users
            private $users;
            private $professor;
            private $connections;

            public function __construct() {
                $this->professor = NULL;
                $this->users = array();
                $this->connections = array();
            }

            public function setProfessor($prof_id) {
                echo "setting professor {$prof_id}\n";
                $this->professor = $prof_id;
            }

            public function addUser($id, $conn) {
                echo "adding user {$id}\n";
                $this->users[$id] = $id;
                $this->connections[$id] = $conn;
            }

            public function getConnections() {
                return array_values($this->connections);
            }

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

            public function removeUser($id) {
                echo "removing user {$id}\n";
                if (isset($this->users[$id])) {
                    unset($this->users[$id]);
                    unset($this->connections[$id]);
                }
            }
        }
    }


