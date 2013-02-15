<?php 
require('class.roundrobin.php');

function shuffle_assoc(&$array) 
{
        $keys = array_keys($array);

        shuffle($keys);

        foreach($keys as $key) {
            $new[$key] = $array[$key];
        }

        $array = $new;
echo 'shuffle_assoc<pre>',print_r($array,true),'</pre><br>';
        return true;
    }


// Let's see how 7 of the best british football teams fight against each other
$teams = array ('Banbury United', 
                'Bashley', 
                'Bedford Town', 
                'Brackley Town', 
                'Cambridge City', 
                'Chippenham Town', 
                'Clevedon Town');

shuffle_assoc($teams);               	
$roundrobin = new roundrobin($teams);

// Generated matches with matchdays and free tickets - because we have an uneven number of teams
echo "<h3>Generated matches with matchdays and free tickets</h3><br />";
$roundrobin->free_ticket_identifer = "FREE TICKET"; //default is "free ticket" 
$roundrobin->create_matches();

// Did everything went right?
if ($roundrobin->finished) {
    $i = 1;
    //Ok, iterating over the matchdays...
    while ($roundrobin->next_matchday()) {
        echo "-------Matchday ".$i."-------<br />";
        //...and the matches of one match day
        while ($match = $roundrobin->next_match()) {
            echo $match[0]."  <b>vs</b>  ".$match[1]."<br />";
        }
        $i++;
        echo"<br />";
    }    
}      



echo "<br /><h3>Generated matches with matchdays but without free tickets</h3><br />";
$roundrobin->free_ticket = false; // free tickets off
$roundrobin->create_matches();

if ($roundrobin->finished) {
    $i = 1;
    while ($roundrobin->next_matchday()) {
        echo "-------Matchday ".$i."-------<br />";
        while ($match = $roundrobin->next_match()) {
            echo $match[0]."  <b>vs</b>  ".$match[1]."<br />";
        }
        $i++;
        echo"<br />";
    }
}



echo "<br /><h3>Generated matches without matchdays and changed teams </h3><br />";
$teams = array('John', 
               'Mike', 
               'Martin', 
               'Ron', 
               'Richard');
               
$roundrobin->pass_teams($teams);
$roundrobin->create_raw_matches();

if ($roundrobin->finished) {
    while ($match = $roundrobin->next_match()) {
        echo $match[0]."  <b>vs</b>  ".$match[1]."<br />";
    }
    echo "<br />";
}


echo "<h3>Simply accessing the matches/matchdays in array format (contains the result from the last match generation)</h3><br />";
echo '<pre>',print_r($roundrobin->matches,true),'</pre><br>';


echo 'Round Robin Schedule: ' . PHP_EOL;
$teams = array ('Banbury United', 
                'Bashley', 
                'Bedford Town', 
                'Brackley Town', 
                'Cambridge City', 
                'Chippenham Town', 
                'Clevedon Town');
echo '<pre>',print_r($roundrobin->generateRRSchedule($teams,true),true),'</pre><br>' ;



?>