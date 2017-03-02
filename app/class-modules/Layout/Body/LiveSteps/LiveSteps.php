<?php


namespace Layout\Body\LiveSteps;


use Icons\Icons;
use PublicException\PublicException;

/**
 *
 * Before you start:
 *      - create a liveSteps service at /services/myservice.php
 *              A live step service accepts two types of requests, which are post requests:
 *                  - get request without parameters: just echoes back the fileName of the "transit file"
 *                  - post request with [file: fileName, clean: true]: will remove the "transit file", use this after the process is done
 *                  - post request with [file: fileName]: will execute the "live steps" service's main goal (which is defined by you)
 *
 *
 *
 *              You can extend this LiveSteps class to ease the implementation.
 *
 *
 *              The service works like this:
 *
 *                  - on the first call, the client asks for the location of the "transit file"
 *                      (which is the file used for communication between the client and the server)
 *                  - once the client knows the location of the "transit file", it then queries the "transit file"
 *                      until the process is finished.
 *                      The messages in the "transit file" are written in the json format.
 *                      The whole transit file is updated every time a new process step is done.
 *                      The json array contains two entries:
 *                          - finished: boolean, whether or not the process is finished,
 *                                  if so, the "close" button will appear on the right top corner of the
 *                                  live steps box.
 *                          - items: an array containing the messages to display
 *                                  each item contains two entries:
 *                                          0: the type of message, which is used as css class (info, error, success)
 *                                          1: the message itself
 *
 *
 * In your html page:
 *      - use LiveSteps:displayContainer to display the live steps container (the html div which will hold
 *              the live messages)
 *      - then, when you need to call the live steps service, use the LiveSteps::displayJsCall method
 *
 *
 *
 * Have a concrete example of implementation with the moduleInstaller.
 *
 *      The service is in /services/modules/moduleInstaller/update-module.php
 *      And the html page is encoded in app-nullos/layout-elements/nullos/modules/moduleInstaller/tabs/modules
 *
 *
 *
 *
 *
 */
class LiveSteps
{

    private $items;
    private $finished;
    private $fileName;

    public function __construct()
    {
        $this->items = [];
        $this->finished = false;
        $this->fileName = null;
    }


    public function listen()
    {
        if (array_key_exists('file', $_POST)) {
            $this->fileName = str_replace('.', '', (string)$_POST['file']);
        } else {
            $fileName = md5(uniqid());
            echo $fileName;
            exit;
        }

        $code = 'ok';
        if (true === $this->acceptRequest()) {

            if (array_key_exists('clean', $_POST)) {
                $f = $this->getFileLocation();
                unlink($f);
            } else {


                try {
                    ob_start();
                    $this->processRequest();
                    $code = ob_get_clean();

                } catch (PublicException $e) {
                    $this->items[] = [
                        'error',
                        $e->getMessage(),
                    ];
                } catch (\Exception $e) {
                    $this->items[] = [
                        'error',
                        \Helper::defaultLogMsg(),
                    ];
                    \Logger::log($e);
                } finally {
                    $this->updateJson();
                }
            }
        } else {
            $code = "permission-denied";
        }
        echo $code;

    }


    protected function acceptRequest()
    {
        return true;
    }

    protected function processRequest()
    {
        return true;
    }

    protected function addMessageInfo($msg)
    {
        $this->items[] = [
            'info',
            $msg,
        ];
        $this->updateJson();
    }

    protected function addMessageError($msg)
    {
        $this->items[] = [
            'error',
            $msg,
        ];
        $this->updateJson();
    }

    protected function addMessageFinish($msg, $addRefreshMessage = false)
    {
        $this->items[] = [
            'success',
            $msg,
        ];
        $this->finished = true;

        if (true === $addRefreshMessage) {
            $this->items[] = [
                'success',
                __('<a href="#" class="reload-current">Click here to refresh the page</a>', "modules/layout/layout"),
            ];
        }
        $this->updateJson();
    }

    public static function displayContainer($id = null)
    {
        if (null === $id) {
            $id = 'live-steps';
        }
        ?>
        <div id="<?php echo $id; ?>" class="live-steps">
            <button class="live-steps-close"><?php Icons::printIcon("clear", "#4a933f"); ?></button>
            <ul></ul>
        </div>
        <?php
    }


    public static function displayJsCall($serviceUrl, $progressDivId = null)
    {
        if (null === $progressDivId) {
            $progressDivId = 'live-steps';
        }
        ob_start();
        ?>
        <script>
            var serviceUrl = "<?php echo $serviceUrl; ?>";
            z.ajaxGet(serviceUrl, function (fileName) {
                var dataUrl = "<?php echo url("/services/modules/layout/progress/"); ?>" + fileName;
                var progressDiv = document.getElementById("<?php echo $progressDivId; ?>");
                progressDiv.classList.add('started');
                progressDiv.addEventListener('click', function (e) {
                    if (e.target.classList.contains("reload-current")) {
                        e.preventDefault();
                        window.location.reload();
                    }
                });


                var id = setInterval(function () {

                    z.ajaxGet(dataUrl, function (msg) {
                        var info = JSON.parse(msg);
                        var items = info.items;
                        var finished = info.finished;

                        if (true === finished) {
                            progressDiv.classList.add("finished");
                            clearInterval(id);
                            z.ajaxPost(serviceUrl, {'file': fileName, 'clean': true}, function (html) {
                                // fetch json
                            });

                        }

                        var ul = progressDiv.querySelector("ul");
                        ul.parentNode.removeChild(ul);
                        ul = document.createElement('ul');
                        progressDiv.appendChild(ul);


                        for (var i in items) {
                            var li = document.createElement('li');
                            var moduleInfo = items[i];
                            li.innerHTML = moduleInfo[1];
                            li.classList.add(moduleInfo[0]);
                            ul.appendChild(li);
                        }
                    });
                }, 1000);

                // execute the main service action
                z.ajaxPost(serviceUrl, {'file': fileName, 'module': module}, function (html) {

                });
            });


        </script>
        <?php
        $s = trim(ob_get_clean());
        $s = substr($s, 8, -9);
        echo $s;
    }

    //------------------------------------------------------------------------------/
    //
    //------------------------------------------------------------------------------/
    private function updateJson()
    {
        $s = json_encode([
            'finished' => $this->finished,
            'items' => $this->items,
        ]);
        $f = $this->getFileLocation();
        file_put_contents($f, $s);
    }

    private function getFileLocation()
    {
        return APP_ROOT_DIR . "/www/services/modules/layout/progress/" . $this->fileName;
    }
}
