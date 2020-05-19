<?php
/*
 You may not change or alter any portion of this comment or credits of
 supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit
 authors.

 This program is distributed in the hope that it will be useful, but
 WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

 /**
 * Module: xForms
 *
 * @package   \XoopsModules\Xforms\include
 * @author    ZySpec <zyspec@yahoo.com>
 * @copyright Copyright (c) 2001-2020 {@link https://xoops.org XOOPS Project}}
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GNU Public License
 * @since     2.00
 */
use XoopsModules\Xforms;
use XoopsModules\Xforms\Helper;

$GLOBALS['xoopsOption']['nocommon'] = true;
//require_once dirname(dirname(dirname(__DIR__))) . '/mainfile.php';
require_once dirname(dirname(dirname(__DIR__))) . '/cpheader.php';
require dirname(__DIR__) . '/preloads/autoloader.php';

$moduleDirName = basename(dirname(__DIR__));

xoops_loadLanguage('admin', $moduleDirName);
xoops_loadLanguage('modinfo', $moduleDirName);
//session_start();

$issuesClass = '\XoopsModules\\' . ucfirst(mb_strtolower($moduleDirName)) . '\Issues';
if (!class_exists($issuesClass)) {
    xoops_load('Issues', $moduleDirName);
}

$modIssues = new $issuesClass();
if ($modIssues->getCachedEtag()) {
    // Found the session var so check to see if anything's changed since last time we checked
    $hdrSize       = $modIssues->execCurl();
    $curl_response = $modIssues->getCurlResponse();

    $status = $modIssues->getHeaderFromArray('Status: ');
    if (preg_match('/^304 Not Modified/', $status)) {
        // Hasn't been modified so get response & header size from session
        $curl_response = isset($_SESSION[$modIssues->getsKeyResponse()]) ? base64_decode(unserialize($_SESSION[$modIssues->getsKeyResponse()])) : [];
        $hdrSize       = isset($_SESSION[$modIssues->getsKeyHdrSize()]) ? unserialize($_SESSION[$modIssues->getsKeyHdrSize()]) : 0;
    } elseif (preg_match('/^200 OK/', $status)) {
        // Ok, request new info
        unset($modIssues);
        $modIssues     = new $issuesClass();
        $hdrSize       = $modIssues->execCurl();
        $curl_response = $modIssues->getCurlResponse();
    } elseif (preg_match('/^403 Forbidden/', $status)) {
        // Probably exceeded rate limit
        $responseArray = explode('\n', $modIssues->getCurlResponse());
        $msgEle        = array_search('message: ', $responseArray);
        if (false !== $msgEle) {
            // Found the error message so set it
            $modIssues->setError(mb_substr($responseArray[$msgEle], 8)); //set the error message
        } else {
            // Couldn't find error message, but something went wrong
            // so clear session vars
            foreach ($modIssues->getsKeyArray() as $key) {
                $_SESSION[$key] = null;
                unset($_SESSION[$key]);
            }
            $modIssues->setError(_AM_XFORMS_ISSUES_ERR_UNKNOWN);
        }
    } else {
        // Unknown error condition - display message & clear session vars
        foreach ($modIssues->getsKeyArray() as $key) {
            $_SESSION[$key] = null;
            unset($_SESSION[$key]);
        }
        $modIssues->setError(_AM_XFORMS_ISSUES_STATUS_UNKNOWN);
    }
} else {
    // Nothing in session so request new info
    $hdrSize       = $modIssues->execCurl();
    $curl_response = $modIssues->getCurlResponse();
}

$hdr        = mb_substr($curl_response, 0, $hdrSize);
$rspSize    = mb_strlen($curl_response) - $hdrSize;
$response   = mb_substr($curl_response, -$rspSize);
$issuesObjs = json_decode($response); //get as objects

$homeIcon = '../../Frameworks/moduleclasses/icons/32/home.png';
// header
//@todo move hard coded language strings to language file
echo '<h1 class="head">Help:'
    . '<a class="ui-corner-all tooltip" href="' . '/modules/' . $moduleDirName . '/admin/index.php' .'"'
        . ' title="Back to the administration of ' .  _MI_XFORMS_NAME . '"> ' . _MI_XFORMS_NAME . ' <img src="' . $homeIcon . '"'
            . ' alt="Back to the Administration of ' .  _MI_XFORMS_NAME . '">'
   . "</a></h1>\n"
   . "<!-- -----Help Content ---------- -->\n"
   . "<h4 class=\"odd\">Report Issues</h4>\n"
   . '<p class="even">'
   . 'To report an issue with the module please go to <a href="' . $modIssues->issueUrl . '" target="_blank">' . $modIssues->issueUrl . '</a>'
   . "</p>\n";

// isue table
echo '<br>'
   . '<h4 class="odd">' . _AM_XFORMS_ISSUES_OPEN . '</h4>'
   . '<p class="even">'
   . '<table>'
   . '  <thead>'
   . '  <tr>'
   . '    <th class="center width10">' . _AM_XFORMS_HELP_ISSUE . '</th>'
   . '    <th class="center width10">' . _AM_XFORMS_HELP_DATE . '</th>'
   . '    <th class="center">' . _AM_XFORMS_HELP_TITLE . '</th>'
   . '    <th class="center">' . _AM_XFORMS_HELP_DESC . '</th>'
   . '    <th class="center width10">' . _AM_XFORMS_HELP_SUBMITTER . '</th>'
   . '  </tr>'
   . '  </thead>'
   . '  <tbody>';

$pullReqFound = false;
$suffix       = '';
$cssClass     = 'odd';
$i = 0;
if (!empty($issuesObjs)) {
    foreach ($issuesObjs as $issue) {
        $suffix = '';
        if (isset($issue->pull_request)) {
            /** @internal {uncomment the following line if you don't want to see pull requests as issues}}}*/
//            continue; // github counts pull requests as open issues so ignore these

            $suffix = '*';
            $pullReqFound = true;
        }

        $dateTimeObj = \DateTime::createFromFormat(\DateTime::ISO8601, $issue->created_at);
        $dispDate    = $dateTimeObj->format('Y-m-d');

        $labelDesc = '';
        if (!empty($issue->labels)) {
            foreach ($issue->labels as $desc) {
                if (!empty($labelDesc)) {
                    $labelDesc .= ', ';
                }
                $labelDesc .= $desc->description;
            }
        }
        ++$i; // issue count
        echo '  <tr>'
           . '    <td class="' . $cssClass . ' center"><a href="' . $issue->html_url . '" target="_blank">' . (int)$issue->number . $suffix . '</a></td>'
           . '    <td class="' . $cssClass . ' center">' . $dispDate . '</td>'
           . '    <td class="' . $cssClass . ' left" style="padding-left: 2em;">' . htmlspecialchars($issue->title, ENT_QUOTES | ENT_HTML5) . '</td>'
           . '    <td class="' . $cssClass . ' left" style="padding-left: 2em;">' . $labelDesc . '</td>'
           . '    <td class="' . $cssClass . ' center"><a href="' . htmlspecialchars($issue->user->html_url, ENT_QUOTES | ENT_HTML5) . '" target="_blank">' . htmlspecialchars($issue->user->login, ENT_QUOTES | ENT_HTML5) . '</a></td>'
           . '  </tr>';
        $cssClass = ('odd' === $cssClass) ? 'even' : 'odd';
    }
}

if (!empty($modIssues->getError())) {
    echo '    <tr><td colspan="4" class="' . $cssClass . ' center bold italic">' . htmlspecialchars($modIssues->getError(), ENT_QUOTES | ENT_HTML5) . '</td></tr>';
} elseif (0 == $i) { // no issues found
    echo '    <tr><td colspan="4" class="' . $cssClass . ' center bold italic">' . _AM_XFORMS_ISSUES_NONE . '</td></tr>';
}

if ($pullReqFound) {
    echo '    <tfoot>'
       . '      <tr><td colspan="4" class="left italic marg3 foot">' . _AM_XFORMS_ISSUES_NOTE . '</td></tr>'
       . '    </tfoot>';
}
echo '    </tbody></table></p>';
