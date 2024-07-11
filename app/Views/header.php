<!-- Der Navigations-Bereich -->
<nav>
    <div class="part1" onclick="home()">
        <img src="images/logo.png" alt="">
        <h1>Prio</h1>
    </div>
    <div class="burger" onclick="responsive()">
            <i class="fas fa-bars"></i>
        </div>
    <div class="part2" id="nav">
        <?php
        $navigationFiller = "/";
        $url = "$_SERVER[HTTP_HOST]"; // gibt die URL
        $havePort = preg_match('/[0-9]/', $url); // die Seite Localhost hat einen bestimmtem Port, daher auch einen Root Ordner

        if (!$havePort) {
            $navigationFiller .= "Prio/";
        }

        // Login
        if (!isset($_SESSION['email']) || $_SESSION['email'] == "") {
            $a = '<button class="loginBtn" onclick="goToLogin()"';
            $a .= '>Login <i class="fas fa-sign-in-alt"></i></button>';
            echo $a;
        } else {
            /* For About Page */
            $a = '<button title="Get informations about this project" onclick="about()"';
            if (preg_match("/about/i", $actual_link)) {
                $a .= ' class="active"';
            }
            $a .= '>About <i class="fas fa-address-card"></i></button>';
            echo $a;

            /* For Admin User */
            if ($_SESSION['role'] == 1) {
                /* Link For Admin Area */
                $a = '<a title="Go to the admin area" href="' . $navigationFiller . 'admin"';
                if (preg_match("/admin/i", $actual_link)) {
                    $a .= ' class="active"';
                }
                $a .= '>Admin Area</a>';
                echo $a;
            }
            /* For Normal And Blocked User */ else if ($_SESSION['role'] == 2 || $_SESSION['role'] == 0) {
                /* If User Is Blocked */
                $a = '<button title="See all your tasks" onclick="aufgaben()"';
                if (preg_match("/home/i", $actual_link)) {
                    $a .= ' class="active"';
                }
                $a .= '>Tasks <i class="fas fa-list-check"></i></button>';
                echo $a;

                /* For Normal User */
                if ($_SESSION['role'] == 0) {
                    /* Link For Time records */
                    $a = '<button title="See all time records" onclick="zeiterfassung()"';
                    if (preg_match("/zeituebersicht/i", $actual_link)) {
                        $a .= ' class="active"';
                    }
                    $a .= '>Time records <i class="fas fa-clock"></i></button>';
                    echo $a;
                }
            }

            // Logout
            $a = '<button title="Log you out from the system" onclick="zuLogout()"';
            if (preg_match("/logout/i", $actual_link)) {
                $a .= ' class="active"';
            }
            $a .= '>Logout <i class="fas fa-sign-out-alt"></i></button>';
            echo $a;

            echo "</div>";
        }
        ?>
    </div>
</nav>