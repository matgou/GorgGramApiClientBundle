<?php
/***************************************************************************
 * Copyright (C) 1999-2012 Gadz.org                                        *
 * http://opensource.gadz.org/                                             *
 *                                                                         *
 * This program is free software; you can redistribute it and/or modify    *
 * it under the terms of the GNU General Public License as published by    *
 * the Free Software Foundation; either version 2 of the License, or       *
 * (at your option) any later version.                                     *
 *                                                                         *
 * This program is distributed in the hope that it will be useful,         *
 * but WITHOUT ANY WARRANTY; without even the implied warranty of          *
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the            *
 * GNU General Public License for more details.                            *
 *                                                                         *
 * You should have received a copy of the GNU General Public License       *
 * along with this program; if not, write to the Free Software             *
 * Foundation, Inc.,                                                       *
 * 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA                   *
 ***************************************************************************/

namespace Gorg\Bundle\GramApiClientBundle\Caller;

use Gorg\Bundle\GramApiClientBundle\Entity\StdApiEntity;


class AccountApiCaller extends AbstractCaller
{
    private function isSha1($str) {
        return (bool) preg_match('/^[0-9a-f]{40}$/i', $str);
    }

    public function createAccount($data)
    {
        if(!is_array($data)) {
            $data = $data->toArray();
        }
        $content = $this->call("POST", "/accounts.json", $data);
        $this->logger->info(sprintf("Create Account on Gram, response : %s", $content));
        if($content) {
            return StdApiEntity::buildFromStdClass(json_decode($content));
        }
        return null;
    }

    public function addUserToGroup($username, $group)
    {
        $content = $this->call("POST", '/accounts/' . $username . '/groups/' . $group . '.json'
);
        $this->logger->info(sprintf("Add User To Group, response : %s", $content));
        if($content) {
            return json_decode($content);
        }
        return null;
    }

    public function updatePassword($username, $password)
    {
        $encodedPassword = sha1($password);
        $data = array(
            'password' => $encodedPassword,
            'hruid'    => $username,
        );
        $content = $this->call("PUT", '/accounts/' . $username . '/accounts.json', $data);
        $this->logger->info(sprintf("Update password on Gram, response : %s", $content));
        if($content) {
            $response = json_decode($content);
            if(isset($response->status) && $response->status == "error") {
                return null;
            } else {
                return $response;
            }
        }
        return null;

    }

    public function update($username, $account)
    {
        if(!is_array($account)) {
            $data = $account->toArray();
        }
        else {
            $data = $account;
        }
        unset($data['hruid']);

        if(!self::isSha1($data['password'])) {
            unset($data['password']);
        }

        $content = $this->call("PUT", '/accounts/' . $username . '/accounts.json', $data);
        $this->logger->info(sprintf("Update password on Gram, response : %s", $content));
        if($content) {
            $response = json_decode($content);
            if(isset($response->status) && $response->status == "error") {
                return null;
            } else {
                return $response;
            }
        }
        return null;
    }

    public function addRole($username, $roleName, $rolePattern)
    {
        $data = array(
            'name'   => $roleName,
            'filter' => $rolePattern,
        );
        $content = $this->call("POST", '/accounts/' . $username . '/role.json', $data);
    }

    public function getRoles($username)
    {
        $content = $this->call("GET", '/accounts/' . $username . '/roles.json');
        if($content) {
            $response = json_decode($content);
            if(isset($response->status) && $response->status == "error") {
                return null;
            } else {
                return $response;
            }
        }
        return null;
    }

    public function search($query = "__all__")
    {
        $content = $this->call("GET", '/accounts/' . $query . '/finds/10/pages/1.json');
        if($content) {
            return json_decode($content);
        }
        return null;
    }

    public function searchUniq($data = array())
    {
        $param = $this->arrayToGetParam($data);
        $content = $this->call("GET", '/search/uniq.json'.$param);

        if ($content) {
            return json_decode($content, true);
        }
        return null;
    }

    public function searchMultiple($data = array())
    {
        $param = $this->arrayToGetParam($data);
        $content = $this->call("GET", '/search/multiple.json'.$param);
        
        if ($content) {
            return json_decode($content, true);
        }
        return null;
    }  

    public function findByEmail($email)
    {
        $content = $this->call("GET", '/emails/' . $email . '/accounts.json');
        if($content) {
            return json_decode($content);
        }
        return null;
    }

    public function findByFirstnameAndLastnameAndBirthday($lastname, $firstname, $birthday)
    {
        $content = $this->call("GET", '/accounts/'.$lastname.'/lastnames/'.$firstname.'/firstnames/'.$birthday.'/birthday.json');
        
        if ($content) {
            return json_decode($content, true);
        }

        return null;
    }
    
    public function findByFirstnameAndLastname($lastname, $firstname)
    {
        $content = $this->call("GET", '/accounts/'.$lastname.'/lastnames/'.$firstname.'/firstname.json');
        
        if ($content) {
            return json_decode($content, true);
        }
        return null;
    }
    public function addMailAccount($username)
    {
        $content = $this->call("POST", '/accounts/' . $username . '/email.json');

        if($content) {
            return json_decode($content);
        }
        return null;
    }

    public function addGappsAccount($username)
    {
        $content = $this->call("POST", '/accounts/' . $username . '/gapps.json');

        if($content) {
            return json_decode($content);
        }
        return null;
    }

    public function addLoginValidationCheck($username, $val)
    {
        $data = array(
            'val'   => $val,
        );

        $content = $this->call("POST", '/accounts/' . $username . '/logincheck.json', $data);

        if($content) {
            return json_decode($content);
        }
        return null;
    }

    public function get($username)
    {
        $content = $this->call("GET", '/accounts/' . $username . '/accounts.json');
        if($content) {
            return StdApiEntity::buildFromStdClass(json_decode($content));
        }
        return null;
    }
}
