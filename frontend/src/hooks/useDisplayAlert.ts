import {useContext, useEffect, useState} from 'react';
import {AppContext} from '../context/AppContext';

export interface AlertDetails {
  severity: 'warning' | 'error' | 'success' | 'info' | undefined;
  text: string;
}

export function useDisplayAlert() {
  const {alertDetails, displayAlert} = useContext(AppContext);

  useEffect(() => {
    setTimeout(() => {
      displayAlert(null);
    }, 5000);
  }, [alertDetails]);

  return displayAlert;
}
