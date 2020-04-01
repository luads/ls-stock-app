import React, {FormEvent, useContext, useState} from 'react';
import {
  Button,
  Dialog,
  DialogActions,
  DialogContent, DialogContentText,
  DialogTitle,
  TableCell,
  TableRow,
  TextField, Typography
} from '@material-ui/core';
import Share from '../../interfaces/Share';
import NumberFormat from 'react-number-format';
import {trackPromise} from 'react-promise-tracker';
import {AppContext} from '../../context/AppContext';
import {AlertDetails, useDisplayAlert} from '../../hooks/useDisplayAlert';
import ShareDetail from '../../interfaces/ShareDetail';
import StockClient from '../../clients/StockClient';

interface Props {
  share: Share;
  stockClient: StockClient;
  reloadShares: any;
  reloadBalance: any;
}

export default function SharesTableItem({ share, stockClient, reloadShares, reloadBalance }: Props) {
  const { user } = useContext(AppContext);
  const displayAlert = useDisplayAlert();
  const [shareDetails, setShareDetails] = useState<ShareDetail | null>(null);
  const [isModalOpen, setModalOpen] = useState(false);
  const [sharesToSell, setSharesToSell] = useState<number>(share.quantity);

  const handleModalClose = () => {
    setModalOpen(false);
  };

  const handleOpenModel = async () => {
    let details = null;

    try {
      details = await trackPromise(stockClient.getShare(user, share.name));
    } catch (error) {
      displayAlert({ severity: 'error', text: error.message });
      return;
    }

    setShareDetails(details);
    setModalOpen(true);
  }

  const handleSubmit = async (e: FormEvent) => {
    e.preventDefault();

    if (!shareDetails || !sharesToSell) {
      return;
    }

    let transactionValue;

    try {
      transactionValue = await trackPromise(stockClient.sell(user, share.name, sharesToSell));
      reloadShares();
      reloadBalance();
    } catch (error) {
      setModalOpen(false);
      displayAlert({ severity: 'error', text: error.message });
      return;
    }

    const alertText = `${sharesToSell} ${share.name} share(s) sold successfully! 
      Transaction value: $${transactionValue.toFixed(2)}`;

    setModalOpen(false);

    displayAlert({
      severity: 'success',
      text: alertText,
    } as AlertDetails)
  };

  return (
    <TableRow>
      <TableCell>{share.name}</TableCell>
      <TableCell>{share.quantity}</TableCell>
      <TableCell>
        <Button size="small" onClick={handleOpenModel}>
          Sell
        </Button>
        {shareDetails ? (
          <Dialog open={isModalOpen} onClose={handleModalClose} aria-labelledby="form-dialog-title">
            <form onSubmit={handleSubmit}>
              <DialogTitle id="form-dialog-title">Selling {share.name} shares</DialogTitle>
              <DialogContent>
                <DialogContentText>
                  The current unit price for a {share.name} share is <strong>
                  <NumberFormat
                    value={shareDetails.price.toString()}
                    displayType="text"
                    thousandSeparator={true}
                    decimalScale={2}
                    fixedDecimalScale={true}
                    prefix="$"/>
                </strong>. Please select the quantity that you want to sell:
                </DialogContentText>
                <TextField
                  required={true}
                  autoFocus
                  margin="dense"
                  id="balance-transaction"
                  label="Amount"
                  type="number"
                  fullWidth
                  value={sharesToSell}
                  onChange={(e) => setSharesToSell(parseInt(e.target.value))}
                /><br/>
                <br/>
                <Typography variant="subtitle1">
                  Sale value: <strong>
                  <NumberFormat
                    value={(shareDetails.price * sharesToSell).toString()}
                    displayType="text"
                    thousandSeparator={true}
                    decimalScale={2}
                    fixedDecimalScale={true}
                    prefix="$"/>
                </strong>
                </Typography>
              </DialogContent>
              <DialogActions>
                <Button onClick={handleModalClose}>
                  Cancel
                </Button>
                <Button color="primary" type="submit">
                  Process
                </Button>
              </DialogActions>
            </form>
          </Dialog>
        ) : ''}
      </TableCell>
      <TableCell align="right">
        {share.value ? (
          <NumberFormat
            value={share.value}
            displayType={'text'}
            thousandSeparator={true}
            decimalScale={2}
            fixedDecimalScale={true}
            prefix={'$'}
          />
        ) : 'Unavailable'}
      </TableCell>
    </TableRow>
  );
}
