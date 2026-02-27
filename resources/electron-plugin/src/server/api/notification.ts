import express from 'express';
import { Notification } from 'electron';
import {notifyPhp} from "../utils.js";
const router = express.Router();

router.post('/', (req, res) => {
    const {
        title,
        body,
        subtitle,
        silent,
        icon,
        hasReply,
        timeoutType,
        replyPlaceholder,
        sound,
        urgency,
        actions,
        closeButtonText,
        toastXml,
        event: customEvent,
        reference,
    } = req.body;

    const eventName = customEvent ?? '\\Native\\Nette\\Events\\Notifications\\NotificationClicked';

    const notificationReference = reference ?? (Date.now() + '.' + Math.random().toString(36).slice(2, 9));

    const notification = new Notification({
        title,
        body,
        subtitle,
        silent,
        icon,
        hasReply,
        timeoutType,
        replyPlaceholder,
        sound,
        urgency,
        actions,
        closeButtonText,
        toastXml
    });

    notification.on("click", (event) => {
        notifyPhp('events', {
            event: eventName || '\\Native\\Nette\\Events\\Notifications\\NotificationClicked',
            payload: {
                reference: notificationReference,
                event: JSON.stringify(event),
            },
        });
    });

    notification.on("action", (event, index) => {
        notifyPhp('events', {
            event: '\\Native\\Nette\\Events\\Notifications\\NotificationActionClicked',
            payload: {
                reference: notificationReference,
                index,
                event: JSON.stringify(event),
            },
        });
    });

    notification.on("reply", (event, reply) => {
        notifyPhp('events', {
            event: '\\Native\\Nette\\Events\\Notifications\\NotificationReply',
            payload: {
                reference: notificationReference,
                reply,
                event: JSON.stringify(event),
            },
        });
    });

    notification.on("close", (event) => {
        notifyPhp('events', {
            event: '\\Native\\Nette\\Events\\Notifications\\NotificationClosed',
            payload: {
                reference: notificationReference,
                event: JSON.stringify(event),
            },
        });
    });

    notification.show();

    res.status(200).json({
        reference: notificationReference,
    });
});

export default router;
