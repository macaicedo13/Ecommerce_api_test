import { useState } from 'react';
import { Container, Row, Col, Table, Button, Card, Alert } from 'react-bootstrap';
import { useNavigate } from 'react-router-dom';
import { useCart } from '../context/CartContext';
import { Trash2, CreditCard, ChevronLeft } from 'lucide-react';
import api from '../api/api';

const CartView = () => {
    const { cart, removeFromCart, cartTotal, clearCart } = useCart();
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState('');
    const navigate = useNavigate();

    const handleCreateOrder = async () => {
        setLoading(true);
        setError('');

        try {
            const items = cart.map(item => ({
                productId: item.id,
                quantity: item.quantity
            }));

            const response = await api.post('/api/orders', { items });

            if (response.status === 201) {
                const orderId = response.data.order.id;
                clearCart();
                navigate(`/orders/${orderId}`);
            }
        } catch (err: any) {
            setError(err.response?.data?.message || 'Error al crear el pedido. Verifique el stock.');
        } finally {
            setLoading(false);
        }
    };

    if (cart.length === 0) {
        return (
            <Container className="mt-5 text-center">
                <h3>Tu carrito está vacío</h3>
                <Button variant="primary" className="mt-3" onClick={() => navigate('/products')}>
                    <ChevronLeft size={18} /> Volver a la tienda
                </Button>
            </Container>
        );
    }

    return (
        <Container fluid className="mt-4 px-4">
            <h2 className="mb-4">Tu Carrito</h2>
            {error && <Alert variant="danger">{error}</Alert>}
            <Row>
                <Col lg={8}>
                    <Table responsive hover className="bg-white rounded shadow-sm">
                        <thead className="table-light">
                            <tr>
                                <th>Producto</th>
                                <th>Precio</th>
                                <th>Cantidad</th>
                                <th>Subtotal</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            {cart.map((item) => (
                                <tr key={item.id}>
                                    <td>{item.name}</td>
                                    <td>${item.price.toFixed(2)}</td>
                                    <td>{item.quantity}</td>
                                    <td>${(item.price * item.quantity).toFixed(2)}</td>
                                    <td>
                                        <Button
                                            variant="outline-danger"
                                            size="sm"
                                            onClick={() => removeFromCart(item.id)}
                                        >
                                            <Trash2 size={16} />
                                        </Button>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </Table>
                </Col>
                <Col lg={4}>
                    <Card className="shadow-sm border-0">
                        <Card.Body>
                            <Card.Title>Resumen del Pedido</Card.Title>
                            <hr />
                            <div className="d-flex justify-content-between mb-3">
                                <span>Subtotal:</span>
                                <span className="fw-bold">${cartTotal.toFixed(2)}</span>
                            </div>
                            <div className="d-flex justify-content-between mb-3">
                                <span>Impuestos (Sugerido):</span>
                                <span>$0.00</span>
                            </div>
                            <hr />
                            <div className="d-flex justify-content-between mb-4">
                                <span className="h5">Total:</span>
                                <span className="h5 text-primary">${cartTotal.toFixed(2)}</span>
                            </div>
                            <Button
                                variant="success"
                                className="w-100 py-2 d-flex align-items-center justify-content-center"
                                disabled={loading}
                                onClick={handleCreateOrder}
                            >
                                <CreditCard size={18} className="me-2" />
                                Continuar al Pago
                            </Button>
                        </Card.Body>
                    </Card>
                </Col>
            </Row>
        </Container>
    );
};

export default CartView;
