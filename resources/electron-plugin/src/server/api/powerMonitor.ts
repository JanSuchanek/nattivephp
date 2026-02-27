import express from 'express'
import { powerMonitor } from 'electron'
import { notifyPhp } from '../utils.js';
const router = express.Router();

router.get('/get-system-idle-state', (req, res) => {
    let threshold = Number(req.query.threshold) || 60;

    res.json({
        result: powerMonitor.getSystemIdleState(threshold),
    })
});

router.get('/get-system-idle-time', (req, res) => {
    res.json({
        result: powerMonitor.getSystemIdleTime(),
    })
});

router.get('/get-current-thermal-state', (req, res) => {
    res.json({
        result: powerMonitor.getCurrentThermalState(),
    })
});

router.get('/is-on-battery-power', (req, res) => {
    res.json({
        result: powerMonitor.isOnBatteryPower(),
    })
});

powerMonitor.addListener('on-ac', () => {
    notifyPhp("events", {
        event: `\\Native\\Nette\\Events\\PowerMonitor\\PowerStateChanged`,
        payload: {
            state: 'on-ac'
        }
    });
})

powerMonitor.addListener('on-battery', () => {
    notifyPhp("events", {
        event: `\\Native\\Nette\\Events\\PowerMonitor\\PowerStateChanged`,
        payload: {
            state: 'on-battery'
        }
    });
})

// @ts-ignore
powerMonitor.addListener('thermal-state-change', (details) => {
    notifyPhp("events", {
        event: `\\Native\\Nette\\Events\\PowerMonitor\\ThermalStateChanged`,
        payload: {
            state: details.state,
        },
    });
})

// @ts-ignore
powerMonitor.addListener('speed-limit-change', (details) => {
    notifyPhp("events", {
        event: `\\Native\\Nette\\Events\\PowerMonitor\\SpeedLimitChanged`,
        payload: {
            limit: details.limit,
        },
    });
})

// @ts-ignore
powerMonitor.addListener('lock-screen', () => {
    notifyPhp("events", {
        event: `\\Native\\Nette\\Events\\PowerMonitor\\ScreenLocked`,
    });
})

// @ts-ignore
powerMonitor.addListener('unlock-screen', () => {
    notifyPhp("events", {
        event: `\\Native\\Nette\\Events\\PowerMonitor\\ScreenUnlocked`,
    });
})


// @ts-ignore
powerMonitor.addListener('shutdown', () => {
    notifyPhp("events", {
        event: `\\Native\\Nette\\Events\\PowerMonitor\\Shutdown`,
    });
})


// @ts-ignore
powerMonitor.addListener('user-did-become-active', () => {
    notifyPhp("events", {
        event: `\\Native\\Nette\\Events\\PowerMonitor\\UserDidBecomeActive`,
    });
})


// @ts-ignore
powerMonitor.addListener('user-did-resign-active', () => {
    notifyPhp("events", {
        event: `\\Native\\Nette\\Events\\PowerMonitor\\UserDidResignActive`,
    });
})

export default router;
