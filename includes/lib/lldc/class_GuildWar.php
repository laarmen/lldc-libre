<?php
namespace LLDC;
//Let's say I'm including the LLDC exception classes, which I don't know how to do.

require_once(FONCTIONS_DIR.'joueur/sendNotification.php');
require_once(INCLUDES_DIR.'lib/lldc/exceptions.php');
class GuildWar {
    // Public interface.
    public function __construct($id, $row=NULL) {
        if ($row === NULL)
            $results = mysql_fetch_assoc(mysql_query('SELECT * FROM guerre_interguilde WHERE idGuerre='.$id.';'));
        else
            $results = $row;
        $this->setId($results['idGuerre']);
        $this->setFirstGuild($results['idGuilde1']);
        $this->setSecondGuild($results['idGuilde2']);
        $this->setBeginning($results['dateDebut']);
        $this->setEnding($results['dateFin']);
        $this->setPeaceDemand($results['demandePaix']);

        $this->_stats = array($results['idGuilde1']=>array(), $results['idGuilde2']=>array());
    }

    public static function NewWar($firstGuildId, $secondGuildId, $beginning) {
        $first = mysql_fetch_assoc(mysql_query(' SELECT idGuilde, nomGuilde FROM guilde WHERE idGuilde='.$firstGuildId));
        $second = mysql_fetch_assoc(mysql_query(' SELECT idGuilde, nomGuilde FROM guilde WHERE idGuilde='.$secondGuildId));
        if ($first === FALSE || $second === FALSE)
            throw new WrongId('One of those guilds doesn\'t exist!');
        $cur = self::lookForCurrentWar($first['idGuilde'], $second['idGuilde']);
        if ($cur === NULL) {
            mysql_query('
                INSERT INTO guerre_interguilde (idGuilde1, idGuilde2, dateDebut, dateFin, demandePaix)
                VALUES ('.$first['idGuilde'].', '.$second['idGuilde'].', '.$beginning.', 0, 0)');
            $sql = mysql_query('SELECT idduj FROM membres WHERE idGuilde='.$first['idGuilde'].' OR idGuilde='.$second['idGuilde']);
            while ($row=mysql_fetch_assoc($sql))
                sendNotification($row['idduj'], 'Guerre de guilde', $first['nomGuilde'].' et '.$second['nomGuilde'].' sont d&eacute;sormais en guerre !');

            return self::lookForCurrentWar($firstGuildId, $secondGuildId);
        }
        return $cur;
    }

    public static function lookForCurrentWar($firstGuildId, $secondGuildId) {
        $res = mysql_fetch_assoc(mysql_query('
            SELECT idGuerre
            FROM guerre_interguilde
            WHERE ((idGuilde1='.$firstGuildId.' AND idGuilde2='.$secondGuildId.')
                OR (idGuilde1='.$secondGuildId.' AND idGuilde2='.$firstGuildId.'))
            AND dateDebut<dateFin'));
        if ($res == FALSE)
            return NULL;
        return new GuildWar($res['idGuerre']);
    }

    public static function lookForWarsOf($guildId, $onlyCurrent=True) {
        $sql_text = 'SELECT * FROM guerre_interguilde WHERE
            (idGuilde1='.$guildId.' OR idGuilde2='.$guildId.')';
        if ($onlyCurrent)
            $sql_text .= ' AND dateDebut<dateFin';
        $sql = mysql_query($sql_text);
        $return_array = array();
        while($row = mysql_fetch_assoc($sql))
            array_push(new GuildWar(0, $row));
        return $return_array;
    }
        
    public function addBattle($battleId) {
        $sql = '
            SELECT m.idGuilde as guildId, r.ecuyer as ecuyer, a.idDefenseur as defenseId
            FROM combats_archives a
                INNER JOIN membres m ON a.idDefenseur=m.idduj
                INNER JOIN guilde_rangs gr ON m.idduj=gr.idduj
                INNER JOIN guilde_rangs_acces r ON gr.idRang=r.idRang
            WHERE a.idArchive='.$battleId; 
        $res = mysql_fetch_assoc(mysql_query($sql));
        if ($this->_firstGuild['idGuilde'] != $res['guildId'] && $this->_secondGuild['idGuilde'] != $res['guildId'])
            throw new WrongId('This is not your war, John !');
        if ($res['ecuyer'] == 1) {
            $sql = '
                SELECT COUNT(a.idArchive) as nb
                FROM combats_archives a
                    INNER JOIN rel_guerre_interguilde_combat g ON a.idArchive=g.idCombat
                WHERE a.idAttaquant='.$res['defenseId'].' AND g.idGuerre='.$this->getId();
            $res = mysql_fetch_assoc(mysql_query($sql));
            if ($res['nb'] == 0)
                return;
        }
        mysql_query('INSERT INTO rel_guerre_interguilde_combat (idCombat, idGuerre) VALUES ('.$battleId.', '.$this->getId().');');
    }

    public function togglePeaceState($guildId) {
        if ($this->_firstGuild != $guildId && $this->_secondGuild != $guildId)
            throw new WrongId('Again, not your war.');
        //TODO
    }
        
    public function isStillOn() { return $this->getBeginning() >= $this->getEnding(); }
    public function getId() { return $this->_warId; }
    public function getFirstGuild() { return $this->_firstGuild;}
    public function getSecondGuild() { return $this->_secondGuild; }
    public function getBeginning() { return $this->_beginning; }
    public function getEnding() { return $this->_ending; }
    public function getVictoriesFirstGuild() {
        return $this->getVictories($this->_firstGuild['guildeId']);
    }
    public function getVictoriesSecondGuild() {
        return $this->getVictories($this->_secondGuild['guildeId']);
    }

    // Private stuff. Keep your hands off me, you perv !
    private $_warId;
    private $_firstGuild;     // As soon as the guilds are objectified, we switch to object reference.
    private $_secondGuild;    // In the mean time, it is a reference to arrays as returned by mysql_fetch_assoc.
    private $_beginning;
    private $_end;
    private $_peaceDemands;
    private $_battles;

    protected function getVictories($guildId) {
        if (!isset($this->_stats[$guildId]['victories'])) {
            $res = mysql_fetch_assoc(mysql_query('
                SELECT COUNT(a.idArchive) as victories
                FROM combats_archives a
                    INNER JOIN rel_guerre_interguilde_combat g ON a.idArchive=g.idCombat
                    INNER JOIN membres m ON a.idVainqueur=m.idduj
                WHERE m.idGuilde='.$guildId));
            $this->_stats[$guildId]['victories'] = $res['victories'];
        }
        return $this->_stats[$guildId]['victories'];
    }

    private function setGuild($guildId, $first=true) {
        if(empty($guildId) || is_null($guildId) || $guildId<0) {
            throw new WrongId('If I were a guild, I wouldn\'t want THAT for ID! (THAT='.$guildId.')'); 
        }
        $guild = mysql_fetch_array(mysql_query('SELECT * FROM guilde where idGuilde='.$guildId.';'));
        if ($first)
            $this->_firstGuild = $guild;
        else
            $this->_secondGuild = $guild;
    }


    private function setFirstGuild($guildId) {$this->setGuild($guildId, true);}
    private function setSecondGuild($guildId) {$this->setGuild($guildId, false);}
    private function setPeaceDemand($nb) {$this->_peaceDemand = $nb;}

    private function setId($id) {
        if(empty($id) || is_null($id) || $id<0) {
            throw new WrongId('Duh ! This war ain\'t happening nor ain\'t been ! (ID='.$id.')');
        }
        $this->_warId = $id;
    }

    private function setBeginning($date) { $this->_beginning = $date; }

}

?>
