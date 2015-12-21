<?php

require_once("$CFG->libdir/formslib.php");
require_once($CFG->libdir . '/completionlib.php');
require_once($CFG->libdir . '/coursecatlib.php');
require_once($CFG->dirroot . '/blocks/webgd_community/lib/class/dao/WebgdCommunityDao.php');

class QuestionForm extends moodleform {

    //Add elements to form
    public function definition() {
        global $CFG;

        $question = null;

        $mform = $this->_form; // Don't forget the underscore!

        $this->dateIntervalScript();
        $this->questionScript();

        $namePage = 'Cadastrar Enquete';
        if ($this->_customdata['question']) {
            $namePage = 'Editar Enquete';

            $webgdCommunityDao = new WebgdCommunityDao();
            $question = $webgdCommunityDao->searchQuestionByCommunityById($this->_customdata['question']);
        }

        $mform->addElement('hidden', 'path_arquivos', $CFG->dataroot . '/blocks/webgd_community' . '/', array('id' => 'path_arquivos'));

        $mform->addElement('hidden', 'community', $this->_customdata['community']);

        $mform->addElement('text', 'nome', "Pergunta");
        $mform->addRule('nome', get_string('labelValidacaoNome', 'block_webgd_community'), 'required', null, 'client');

        $mform->addElement('text', 'from', 'Data Inicio', array("id" => "from"));
        $mform->addRule('from', null, 'required', null, 'client');

        $mform->addElement('text', 'to', 'Data Fim', array("id" => "to"));
        $mform->addRule('to', null, 'required', null, 'client');

        $options = array(
            '1' => 'Sim',
            '0' => 'Não',
        );

        $mform->addElement('select', 'enable', 'Habilitado', $options);

        $mform->addElement('filepicker', 'attachmentQuestion', 'Pergunta em Libras', null, array('accepted_types' => '*'));

        $nameButton = 'Salvar';

        $mform->addElement("html", "<div style='background: #f5f5f5; margin-bottom:-30px;'>");
        $mform->addElement('file', 'attachmentAnswer', 'Respostas em Libras', null, array('accepted_types' => '*'));
        $mform->addElement('hidden', 'video', array("id" => "video_hidden"));
        $mform->addElement("html", "</div>");

        $buttonarray = array();
        $buttonarray[] = & $mform->createElement('text', 'pergunta', 'Adicionar Resposta', array("id" => "nome_pergunta"));
        $buttonarray[] = & $mform->createElement('button', 'bt_pergunta', 'Adicionar', array("id" => "bt_pergunta"));
        $mform->addGroup($buttonarray, 'buttonar', 'Adicionar Resposta', array(' '), false);

        if (!empty($question)) {
            $nameButton = 'Salvar';
            $mform->setDefault("nome", $question->name);
            $mform->setDefault("from", date("d/m/Y", $question->startdate));
            $mform->setDefault("to", date("d/m/Y", $question->enddate));
            $mform->setDefault("enable", $question->enabled);

            $mform->addElement('hidden', 'question', $this->_customdata['question']);
            $answers = $webgdCommunityDao->searchAskQuestionByCommunityById($question->id);
            $mform->addElement("html", "<div id='perfuntas'>");
            foreach ($answers as $dataAnswer) {
                $rand = rand();
                $mform->addElement("html", "<div id=" . $rand . " >Resposta: " . $dataAnswer->name_question . " <a href='#' onclick=\"remover('" . $rand . "')\"><input type=\"hidden\" value='" . $dataAnswer->name_question . "' name=\"pergunta[]\"><input type=\"hidden\" value='" . $dataAnswer->video . "' name=\"video[]\"> remover</a><br></div>");
            }

            $mform->addElement("html", "</div>");
        } else {

            $mform->addElement("html", "<div id='perfuntas'></div>");
        }

        $buttonarray = array();
        $buttonarray[] = &$mform->createElement('submit', 'submitbutton', $nameButton);
        //$buttonarray[] = &$mform->createElement('reset', 'resetbutton', get_string('revert'));
        $buttonarray[] = &$mform->createElement('button', 'cancelar', 'Cancelar', 'onclick=location.href="' . $CFG->wwwroot . '/blocks/webgd_community/view.php?community=' . $this->_customdata['community'] . '"');
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $mform->closeHeaderBefore('buttonar');
    }

    private function dateIntervalScript() {
        ?>
        <script>
            $(function () {
                $("#from").datepicker({
                    defaultDate: "+1w",
                    changeMonth: true,
                    numberOfMonths: 3,
                    dateFormat: "dd/mm/yy",
                    onClose: function (selectedDate) {
                        $("#to").datepicker("option", "minDate", selectedDate);
                    }
                });
                $("#to").datepicker({
                    defaultDate: "+1w",
                    changeMonth: true,
                    numberOfMonths: 3,
                    dateFormat: "dd/mm/yy",
                    onClose: function (selectedDate) {
                        $("#from").datepicker("option", "maxDate", selectedDate);
                    }
                });
            });
        </script>
        <?php

    }

    private function questionScript() {
        ?>
        <script>
            $(function () {

                var form;
                var preenchido = 0;
                $('#id_attachmentAnswer').change(function (event) {
                    form = new FormData();
                    form.append('fileUpload', event.target.files[0]);
                    var name = event.target.files[0].name;
                    form.append('name', name);
                    preenchido = 1;
                });

                $("#bt_pergunta").click(function () {
                    var name = $.trim($("#nome_pergunta").val());
                    if (name == "") {
                        alert("resposta em branco");
                    } else {
                        //form.append('teste', "5");
                        if (preenchido == 1) {
                            $.ajax({
                                url: 'saveVideoQuestion.php',
                                data: form,
                                processData: false,
                                contentType: false,
                                type: 'POST',
                                success: function (retorno) {
                                    $("#nome_pergunta").val("");
                                    $("#id_attachmentAnswer").val("");
                                    preenchido = 0;
                                    var id = Math.floor(Math.random() * 600) + 10;
                                    $("#perfuntas").append("<div id=" + id + " >Resposta: " + name + " <a href='#' onclick=\"remover('" + id + "')\"><input type=\"hidden\" value='" + name + "' name=\"pergunta[]\"><input type=\"hidden\" value='" + retorno + "' name=\"video[]\"> remover</a><br></div>");
                                }
                            });
                        } else {
                            $("#nome_pergunta").val("");
                            $("#id_attachmentAnswer").val("");
                            preenchido = 0;
                            var id = Math.floor(Math.random() * 600) + 10;
                            $("#perfuntas").append("<div id=" + id + " >Resposta: " + name + " <a href='#' onclick=\"remover('" + id + "')\"><input type=\"hidden\" value='" + name + "' name=\"pergunta[]\"><input type=\"hidden\" value='' name=\"video[]\"> remover</a><br></div>");
                        }
                    }
                });
            });

            function remover(id) {
                document.getElementById(id).remove();
                alert("resposta removida com sucesso");
            }
        </script>

        <?php

    }

    //Custom validation should be added here
    function validation($data, $files) {
        return array();
    }

}
