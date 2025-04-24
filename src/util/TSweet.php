<?php

namespace GX4\Util;

use Adianti\Control\TAction;
use Adianti\Widget\Base\TScript;

/**
 * TSweet
 *
 * Classe utilitária para exibição de mensagens e notificações utilizando SweetAlert2 com Adianti Framework.
 */
class TSweet
{
    /**
     * Exibe uma mensagem simples com botão de confirmação.
     *
     * @param string        $title         Título da mensagem
     * @param string        $text          Texto do corpo
     * @param string        $icon          Ícone do alerta (success, error, warning, info, question)
     * @param string        $textButton    Texto do botão de confirmação
     * @param TAction|null  $action        Ação a ser executada após confirmar
     * @param string        $colorButton   Cor do botão de confirmação
     */
    public static function showMessage(
        string $title,
        string $text,
        string $icon = 'success',
        string $textButton = 'OK',
        TAction $action = null,
        string $colorButton = '#236BB0'
    ): void {
        $callback = 'undefined';

        if ($action) {
            $callback = "__adianti_load_page('{$action->serialize()}')";
        }

        TScript::create("Swal.fire({
            allowOutsideClick: false,
            allowEscapeKey: false,
            title: '{$title}',
            html: '{$text}',
            icon: '{$icon}',
            confirmButtonText: '{$textButton}',
            confirmButtonColor: '{$colorButton}'
        }).then((result) => {
            if (result.isConfirmed) {
                $callback
            }
        })");
    }

    /**
     * Exibe um alerta de confirmação com opções de confirmar ou cancelar.
     *
     * @param string        $title              Título do alerta
     * @param string        $text               Texto do alerta
     * @param TAction       $actionConfirm      Ação ao confirmar
     * @param string        $textButtonConfirm  Texto do botão confirmar
     * @param string        $textButtonCancel   Texto do botão cancelar
     * @param string        $icon               Ícone do alerta
     * @param string        $focusConfirm       Se o botão confirmar deve ter foco
     * @param TAction|null  $actionCancel       Ação ao cancelar
     * @param string        $colorButtonConfirm Cor do botão confirmar
     * @param string        $colorButtonCancel  Cor do botão cancelar
     */
    public static function confirm(
        string $title,
        string $text,
        TAction $actionConfirm,
        string $textButtonConfirm = 'Confirmar',
        string $textButtonCancel = 'Cancelar',
        string $icon = 'question',
        string $focusConfirm = 'false',
        TAction $actionCancel = null,
        string $colorButtonConfirm = '#236BB0',
        string $colorButtonCancel = '#d14529'
    ): void {
        $callbackConfirm = "__adianti_load_page('{$actionConfirm->serialize()}')";
        $callbackCancel = 'undefined';

        if ($actionCancel) {
            $callbackCancel = "__adianti_load_page('{$actionCancel->serialize()}')";
        }

        TScript::create("Swal.fire({
            allowOutsideClick: false,
            allowEscapeKey: false,
            title: '{$title}',
            html: '{$text}',
            icon: '{$icon}',
            showCancelButton: true,
            confirmButtonText: '{$textButtonConfirm}',
            confirmButtonColor: '{$colorButtonConfirm}',
            cancelButtonText: '{$textButtonCancel}',
            cancelButtonColor: '{$colorButtonCancel}',
            focusConfirm: {$focusConfirm}
        }).then((result) => {
            if (result.isConfirmed) {
                $callbackConfirm
            } else if (result.isDenied) {
                $callbackCancel
            }
        })");
    }

    /**
     * Exibe um toast de notificação.
     *
     * @param string        $title           Título do toast
     * @param string        $icon            Ícone (success, error, warning, info, question)
     * @param string        $position        Posição (top-end, top-start, bottom, etc.)
     * @param int           $timer           Tempo em milissegundos para esconder automaticamente
     * @param TAction|null  $actionClose     Ação ao fechar o toast
     */
    public static function toast(
        string $title,
        string $icon = 'success',
        string $position = 'top-end',
        int $timer = 3000,
        TAction $actionClose = null
    ): void {
        $callbackActionClose = 'undefined';

        if ($actionClose) {
            $callbackActionClose = "__adianti_load_page('{$actionClose->serialize()}')";
        }

        TScript::create("Toast = Swal.mixin({
            toast: true,
            position: '{$position}',
            showConfirmButton: false,
            timer: {$timer},
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer),
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            },
            didClose: (toast) => {
                $callbackActionClose
            }
        });
        Toast.fire({
            icon: '{$icon}',
            title: '{$title}'
        });");
    }
}
