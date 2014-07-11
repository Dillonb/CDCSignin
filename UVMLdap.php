<?php
// $Id: UVMLdap.php,v 1.7 2005/12/01 15:27:58 fcs Exp $
//
// $Log: UVMLdap.php,v $
// Revision 1.7  2005/12/01 15:27:58  fcs
// Update to hide attributes we don't wish to display
//
// Revision 1.6  2003/01/24 17:08:03  fcs
// Update to display ALL editable attributes not just the printable ones
// Also generate a WARNING if the entry has privacy requested
//
// Revision 1.5  2002/12/06 14:46:39  fcs
// Updated to have NOTE about you can see all your info when you
// authenticate and your search returns your own entry.
//
// Revision 1.4  2002/11/18 20:05:25  fcs
// Update to support '$' separators in address attributes
// Also update to support the "SeeAlso" attribute
//
// Revision 1.3  2002/11/18 19:58:50  fcs
// Reintegrate production code
//
// Revision 1.2  2002/11/05 16:13:47  fcs
// making it work...
//
// Revision 1.1  2002/11/04 18:18:35  fcs
// Initial revision
//
//=================================================

class UVMLdap {

  // ldap defaults
  var $ldap_server = 'ldap.uvm.edu';
  var $ldaprw_server = 'ldaprw.uvm.edu';
  var $ldap_port = 389;
  var $ldap_base = 'dc=uvm,dc=edu';
  var $ldap_login_base = 'dc=uvm,dc=edu';
  var $BASE_PAGE = "https://www.uvm.edu/directory/";
  #	var $BASE_PAGE = "https://www.uvm.edu/~fcs/people/";

  var $schema = array (
    'cn' => 'Name',
    'facsimiletelephonenumber' => 'Fax',
    'displayname' => 'Preferred Full Name',
    'dn' => 'Distinguished Name',
    'labeleduri' => 'Web Address',
    'mobile' => 'Cellular Phone Number',
    'givenname' => 'First Name',
    'localityname' => 'Locality',
    'mail' => 'E-mail Address',
    'o' => 'Organization',
    'ou' => 'Department',
    'pager' => 'Pager',
    'preferredlanguage' => 'Preferred Language',
    'postaladdress' => 'Postal Address',
    'homepostaladdress' => 'Home Postal Address',
    'homephone' => 'Home Telephone Number',
    'sn' => 'Last Name',
    'st' => 'State',
    'telephonenumber' => 'Telephone Number',
    'title' => 'Title',
    'netid' => 'UVM Network ID',
    'uid' => 'UVM Network ID',
    'usercertificate' => 'X.509 Certificate',
    'usersmimecerticiate' => 'S/MIME Certificate',
    'uvmedualias' => 'Email Address(es)',
    'uvmedubroadcastflag' => 'UVM Broadcast Flag',
    'uvmeduprivate' => 'UVM Privacy',
    'uvmedupersonalprivacy' => 'Employee Directory Exclusion (NOT FERPA)',
    'uvmeduofficephone' => 'Office Phone',
    'uvmeduofficeaddress' => 'Office Address',
    'uvmeduofficelocation' => 'Office Location',
    'uvmedufamily' => 'Family',
    'uvmeduhighschool' => 'High School',
    'uvmeducolleges' => 'Colleges',
    'uvmeduhours' => 'Hours',
    'uvmeduproject' => 'Project',
    'uvmsiclass' => 'Class',
    'uvmsicollege' => 'College',
    'uvmsifirstname' => 'First Name',
    'uvmsilasttermattended' => 'Last Attended',
    'uvmsimiddlename' => 'Middle Name',
    'uvmsilastname' => 'Last Name',
    'uvmsimajor' => 'Major',
    'uvmsiname' => 'Full Name',
    'uvmsilocaladdress' => 'Local Address',
    'uvmsipermanentaddress' => 'Permanent Address',
    'uvmsibirthday' => 'Birthday',
    'uvmsilocalphone' => 'Local Phone',
    'uvmsipermanentphone' => 'Permanent Phone',
    'uvmsistatus' => 'Status',
    'uvmsipidm' => 'PIDM',
    'uvmhrdepartment' => 'Department',
    'uvmhrfirstname' => 'First Name',
    'uvmhrlastname' => 'Last Name',
    'uvmhrmiddlename' => 'Middle Name',
    'uvmhrname' => 'Full Name',
    'uvmhrofficeaddress' => 'Office Address',
    'uvmhrofficephone' => 'Office Phone',
    'uvmhrterminationdate' => 'Termination Date',
    'uvmhrtitle' => 'Employment Title',
    'uvmhrhiredate' => 'Employment Date',
    'uvmhrterminationdate' => 'Termination Date',
    'uvmhrhomeaddress' => 'Home Address',
    'uvmhrhomephone' => 'Home Telephone Number',
    'uvmhrlastpaiddate' => 'Date of Last Paycheck',
    'edupersonnickname' => 'Nickname',
    'edupersonprincipalname' => 'Authentication Name',
    'uvmeduprimaryaffiliation' => 'Primary Affiliation',
    'uvmeduaffiliation' => 'Affiliation(s)',
    'edupersonprimaryaffiliation' => 'EduCause Primary Affiliation',
    'edupersonaffiliation' => 'EduCause Affiliation(s)',
    'description' => 'Other Information',
    'userpassword' => 'Password',
    'uvmaltuid' => 'Open Lab UID Number',
    'uvmaltgid' => 'Open Lab GID Number',
    'uvmalthomedir' => 'Open Lab Home Directory',
    'objectclass' => 'LDAP Object Class(es)',
    'mailroutingaddress' => 'Internal Mail Delivery Address',
    'maillocaladdress' => 'Externally Visible Mail Address(es)',
    'uvmhrbarcode' => 'UVM ID Bar Code',
    'uvmhrbirthday' => 'Birth Date',
    'uvmhrbpidm' => 'Student Information PIDM',
    'uvmhrid' => 'Employee ID',
    'uvmhrjobcode' => 'Employee Type',
    'uvmhrjobsubcode' => 'Employee Sub-Type',
    'uvmhrprivate' => 'Privacy Level Requested',
    'uvmsibarcode' => 'UVM ID Bar Code',
    'uvmsiid' => 'Student ID',
    'uvmsiprivate' => 'FERPA Privacy',
    'seealso' => 'See Also'
  );



  /**
   * Normally printed attributes
   */
  var $printable = array(
    'cn' => 1,
    'displayname' => 1,
    'edupersonnickname' => 1,
    'uvmedualias' => 1,
    'title' => 1,
    'ou' => 1,
    'postaladdress' => 1,
    'uvmeduofficeaddress' => 1,
    'uvmeduofficelocation' => 1,
    'telephonenumber' => 1,
    'uvmeduofficephone' => 1,
    'facsimiletelephonenumber' => 1,
    'pager' => 1,
    'homepostaladdress' => 1,
    'homephone' => 1,
    'uvmeduprimaryaffiliation' => 1,
    'uvmeduaffiliation' => 1,
    'uvmeduhours' => 1,
    'uvmeduproject' => 1,
    'uvmedufamily' => 1,
    'uvmeduhighschool' => 1,
    'uvmeducolleges' => 1,
    'labeleduri' => 1,
    'description' => 1,
    'netid' => 1,
    'uid' => 1
  );
  #		'uvmhrterminationdate' => 1,


  // This is the list of attributes that are editable
  // which are also allowed to have multiple values
  // in the LDAP entry
  var $multivalue = array(
    "description" => 1,
    "edupersonnickname" => 1,
    "facsimiletelephonenumber" => 1,
    "labeleduri" => 1,
    "uvmeducolleges" => 1,
    "uvmeduofficeaddress" => 1,
    "uvmeduproject" => 1,
    "postaladdress" => 1,
    "telephonenumber" => 1,
    "homephone" => 1,
    "homepostaladdress" => 1,
    "pager" => 1,
    "uvmedufamily" => 1,
    "uvmeduhighschool" => 1
  );

  // This is the list of attributes that are editable for
  // ou=Aliases entries
  var $alias_editable = array(
    "displayname" => 1,
    "description" => 1,
    "edupersonnickname" => 1,
    "facsimiletelephonenumber" => 1,
    "labeleduri" => 1,
    "uvmedubroadcastflag" => 1,
    "uvmeducolleges" => 1,
    "uvmeduhours" => 1,
    "uvmeduofficeaddress" => 1,
    "uvmeduofficelocation" => 1,
    "uvmeduofficephone" => 1,
    "uvmeduproject" => 1,
    "postaladdress" => 1,
    "telephonenumber" => 1,
    "homephone" => 1,
    "homepostaladdress" => 1
  );

  // This is the list of attributes that are editable for
  // ou=People entries
  var $people_editable = array(
    "displayname" => 1,
    "description" => 1,
    "edupersonnickname" => 1,
    "facsimiletelephonenumber" => 1,
    "labeleduri" => 1,
    "pager" => 1,
    "uvmedubroadcastflag" => 1,
    "uvmeducolleges" => 1,
    "uvmedufamily" => 1,
    "uvmeduhighschool" => 1,
    "uvmeduhours" => 1,
    "uvmeduofficeaddress" => 1,
    "uvmeduofficelocation" => 1,
    "uvmeduofficephone" => 1,
    "uvmeduproject" => 1
  );
  //"uvmedupersonalprivacy" => 1,

  // This is the list of attributes that although people can see
  // them with a proper ldap search, we'll never display them.
  var $to_be_hidden = array(
    "gecos" => 1,
    "uidnumber" => 1,
    "loginshell" => 1,
    "homedirectory" => 1,
    "gidnumber" => 1,
    'edupersonprimaryaffiliation' => 1,
    'edupersonaffiliation' => 1,
    "uvmaltuid" => 1,
    "uvmaltgid" => 1,
    "uvmalthomedir" => 1,
    "mailroutingaddress" => 1
  );

  var $ldap = false;
  var $cracker = "";
  var $dn = "";
  var $netid = "";
  var $password = "";


  /**
   * constructor
   */
  function UVMLdap($server = "ldap.uvm.edu", $port = 389)
  {
    $this->ldapConnect($server, $port);
  }

  /**
   * makeFilter()
   * takes an attribute, a filter ('=', '~=', ..), and a string
   * and returns a filter
   *
   * @param	$attr	string	attribute
   * @param	$filter	string	filter
   * @param	$str	string	string value to match
   */
  function makeFilter($attr, $filter, $str)
  {
    return "({$attr}{$filter}{$str})";
  }

  /**
   * makeCombinedFilter()
   * takes an attribute, operator ('|', '&', ..), a filter ('=',
   * '~=', ..), and a string and returns a compiled combined filter
   * in prefix notation
   *
   * @param	$attr	string	attribute
   * @param	$op	string	operator
   * @param	$value	string	string value to match
   * @param	$filter	string	filter
   */
  function makeCombinedFilter($attr, $op, $value, $filter)
  {
    $value = $this->escape($value);
    if (is_array($value)) {
      $filter = $this->combineStatements($op, $attr, $filter, $value);
    } else {
      $filter .= $this->makeFilter($attr, $filter, $value);
    }
    return $filter;
  }

  /**
   * escape()
   * escapes the ldap filter special characters with '\'
   *
   * @param	$value	string	string
   */
  function escape($value)
  {
    return str_replace(array('(', ')', "\\\\", "\0"),
      array('\\(', '\\)', "\\0"), $value);
  }

  /**
   * makeGenericFilter()
   * takes an attribute, operator ('|', '&', ..), a filter ('=',
   * '~=', ..), and a value and returns a generic
   * search filter in prefix notation
   *
   * @param	$op	string	operator
   * @param	$attr	string	attribute
   * @param	$filter	string	filter
   * @param	$word	string	wordvalue to search for
   */
  function makeGenericFilter($op, $attr, $filter, $word)
  {
    $filters = array();
    if (strstr($word, '*')) {
      return $this->makeFilter($attr,$filter,$word);
    } else {
      $filters[] = $this->makeFilter($attr, $filter, $word);
      if ($filter != "~=")
      {
        if ( strlen($word) > 3 ) {
          $filters[] = $this->makeFilter($attr, $filter, "*$word*");
        }
        $filters[] = $this->makeFilter($attr, $filter, "$word*");
        $filters[] = $this->makeFilter($attr, $filter, "*$word");
      }
      return $this->prefix($op, $filters);
    }
  }

  /**
   * combineStatements()
   * takes an attribute, operator ('|', '&', ..), a filter ('=',
   * '~=', ..), and a an array of values and returns a compiled
   * combined filter in prefix notation
   *
   * @param	$op	string	operator
   * @param	$attr	string	attribute
   * @param	$filter	string	filter
   * @param	$values	array	array of values to match
   */
  function combineStatements($op, $attr, $filter, $values)
  {
    $filters = array();
    foreach ($values as $word) {
      $filters[] = $this->makeFilter($attr, $filter, $word);
    }
    return $this->prefix($op, $filters);
  }

  /**
   * prefix()
   * takes an operator ('|', '&', ..), and an array of filters
   * and combines the filters
   *
   * @param	$op	string	operator
   * @param	$filters	array	array of filters
   */
  function prefix($op, &$filters)
  {
    if ( sizeof($filters) > 1 ) {
      $filter = "($op";
      while (sizeof($filters) > 0) {
        $filter .= array_pop($filters);
      }
      $filter .= ")";
    } else {
      $filter = array_pop($filters);
      if (strncmp($filter, "(", 1) !== 0) {
        $filter = "(" . $filter . ")";
      }
    }
    return $filter;
  }

  function SYNTAXpostalAddress($address)
  {
    return implode("\n", explode('$',$address));
  }

  function formatPostalAddress($address)
  {
    return $this->SYNTAXpostalAddress($address);
  }

  function formatHomePostalAddress($address)
  {
    return $this->SYNTAXpostalAddress($address);
  }

  function formatuvmSILocalAddress($address)
  {
    return $this->SYNTAXpostalAddress($address);
  }

  function formatuvmSIPermanentAddress($address)
  {
    return $this->SYNTAXpostalAddress($address);
  }

  function formatuvmHROfficeAddress($address)
  {
    return $this->SYNTAXpostalAddress($address);
  }

  function formatuvmHRHomeAddress($address)
  {
    return $this->SYNTAXpostalAddress($address);
  }

  function ldapConnect($server = "ldap.uvm.edu", $port = 389)
  {
    // Connect to the ldap server
    $ldap = @ldap_connect($server, $port);
    if ( ! $ldap ) {
      $error = ldap_error($ldap);
      print "<p>Error: $error<br />\n";
      fatal_error($error);
    }

    // security encryption requires LDAP Version 3
    if ( ! @ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3) ) {
      $error = ldap_error($ldap);
      print "<p>Error: $error<br />\n";
      ldap_close($ldap);
      fatal_error($error);
    }

    // Turn on SSL encryption
    if ( ! @ldap_start_tls($ldap) ) {
      $error = ldap_error($ldap);
      print "<p>Error: $error<br />\n";
      print "<p>Server: $server Port: $port\n";
      ldap_close($ldap);
      fatal_error($error);
    }

    $this->ldap = $ldap;
    return $ldap;
  }


  function is_editable($dn, $attribute)
  {
    // Determine the type of dn the attribute is in
    $chunks = explode(',', $dn);
    if ( strcasecmp($chunks[1], "ou=aliases") == 0 ) {
      // It's an alias...
      return isset($this->alias_editable[$attribute]);
    } else {
      // It's a person...
      return isset($this->people_editable[$attribute]);
    }

  }


  function dn_type($dn)
  {
    // Determine the type of dn we were passed
    $chunks = explode(',', $dn);
    if ( strcasecmp($chunks[1], "ou=aliases") == 0 ) {
      return "alias";
    } else {
      return "people";
    }
  }


  function is_multivalued($attribute)
  {
    // Now, see if the attribute is multivalued
    return isset($this->multivalue[$attribute]);
  }


  function is_printable($attribute)
  {
    // Now, see if the attribute is printable
    if ( isset($this->to_be_hidden[$attribute]) ) {
      return 1;
    } else {
      return isset($this->printable[$attribute]);
    }
  }


  function auth($dn = "", $netid="", $password="")
  {
    //echo "<br/>Attempting auth<br/>";
    if (!$this->ldap)
    {
      // Connect to the ldap server
      $this->ldap = ldapConnect();
    }

    if ( $dn == "" )
    {
      // Generate the search filter to find the
      // DN we are trying to log in as
      $filter = $this->makeFilter("netid", "=", $netid);

      // Find it...
      $result = @ldap_search($this->ldap, $this->ldap_login_base, $filter);

      // Make sure we got one and only one...
      if ( $result )
      {
        $count = @ldap_count_entries($this->ldap, $result);
        if ($count === 1)
        {
          $entry = @ldap_get_entries($this->ldap, $result);
          if ( !$entry )
          {
            // Uhoh... no entries were returned... something broke
            $error = ldap_error($this->ldap);
            print "<p>Error: " . $error . "<br />\n";
            return False;
          }
          $this->dn = $entry[0]["dn"];
          $this->netid = $netid;

          // Ok, now verify the dn/password combination...
          ldap_close($this->ldap);
          $this->ldap = $this->ldapConnect();
          $bind = @ldap_bind($this->ldap, $this->dn, $password);
          if (!$bind)
          {
            // User screwed up...
            $error = ldap_error($this->ldap);
            print "<p>Error: " . $error . "<br />\n";
            return False;
          }
          $this->password = $password;
          $this->cracker = $this->makeCookie($this->dn, $this->password);
          //echo "<br/>cookie made: {$this->cracker}<br/>";
        }
        elseif ($count === 0 )
        {
          print "<p>Error: Invalid ID and/or Password.<br />";
          return False;
        }
        else
        {
          print "<p>Attribute netid: '{$netid}' is not unique.";
          return False;
        }
      }
      else
      {
        print "<p>Error: " . ldap_error($this->ldap) . "<br />\n";
        return False;
      }
    }
    else
    {
      // We were passed a DN, let's see if it works :-)
      $bind = @ldap_bind($this->ldap, $dn, $password);
      if (!$bind)
      {
        // User screwed up...
        $error = ldap_error($this->ldap);
        print "<p>Error: ${error}<br />\n";
        return False;
      }
      if ( strcmp($dn, $this->dn) )
      {
        $this->dn = $dn;
        $this->password = $password;
      }
    }
    return True;
  }

  //
  // filter_escape -- escape the special characters that will
  //	cause problems in the LDAP search
  function filter_escape($value)
  {
    return str_replace(array('\\',   '(',    ')',    "\0"),
      array('\\5c', '\\28', '\\29', '\\00'),
      $value);
  }

  function filter_full_escape($value)
  {
    return str_replace(array('\\',   '(',    ')',    '*',    "\0"),
      array('\\5c', '\\28', '\\29', '\\2a', '\\00'),
      $value);
  }

}

