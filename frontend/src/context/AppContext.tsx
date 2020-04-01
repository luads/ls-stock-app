import React from 'react';
import {AlertDetails} from '../hooks/useDisplayAlert';

interface AppContextProps {
  user: string;
  alertDetails: AlertDetails | null;
  displayAlert: any;
}

const initialState = {
  user: '',
  alertDetails: null,
  displayAlert: () => {},
} as AppContextProps;

export const AppContext = React.createContext<AppContextProps>(initialState);
