import { useState, useEffect } from 'react';
import { Container, Row, Col, Card, Form, Button, Table, Badge, Alert, Spinner } from 'react-bootstrap';
import { PlusCircle, ShoppingBag } from 'lucide-react';
import api from '../api/api';

const AdminDashboard = () => {
    const [orders, setOrders] = useState<any[]>([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState('');
    const [success, setSuccess] = useState('');

    // Form state for new product
    const [newProduct, setNewProduct] = useState({
        name: '',
        description: '',
        price: '',
        stock: ''
    });

    const fetchAllOrders = async () => {
        try {
            const response = await api.get('/api/orders');
            setOrders(response.data.orders || []);
        } catch (err) {
            setError('Error al cargar pedidos globales');
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchAllOrders();
    }, []);

    const handleCreateProduct = async (e: React.FormEvent) => {
        e.preventDefault();
        setError('');
        setSuccess('');
        try {
            await api.post('/api/products', {
                ...newProduct,
                price: parseFloat(newProduct.price),
                stock: parseInt(newProduct.stock)
            });
            setSuccess('Producto creado exitosamente');
            setNewProduct({ name: '', description: '', price: '', stock: '' });
        } catch (err: any) {
            setError(err.response?.data?.message || 'Error al crear producto');
        }
    };

    const getStatusBadge = (status: string) => {
        switch (status) {
            case 'completed': return <Badge bg="success">Completado</Badge>;
            case 'pending': return <Badge bg="warning" text="dark">Pendiente</Badge>;
            default: return <Badge bg="secondary">{status}</Badge>;
        }
    };

    return (
        <Container fluid className="mt-4 px-4">
            <h2 className="mb-4">Panel de Administración</h2>

            {error && <Alert variant="danger" dismissible onClose={() => setError('')}>{error}</Alert>}
            {success && <Alert variant="success" dismissible onClose={() => setSuccess('')}>{success}</Alert>}

            <Row>
                {/* Column to Create Product */}
                <Col lg={4} className="mb-4">
                    <Card className="shadow-sm border-0">
                        <Card.Body>
                            <Card.Title className="d-flex align-items-center mb-4">
                                <PlusCircle className="me-2 text-primary" /> Crear Nuevo Producto
                            </Card.Title>
                            <Form onSubmit={handleCreateProduct}>
                                <Form.Group className="mb-3">
                                    <Form.Label>Nombre</Form.Label>
                                    <Form.Control
                                        type="text"
                                        required
                                        value={newProduct.name}
                                        onChange={e => setNewProduct({ ...newProduct, name: e.target.value })}
                                    />
                                </Form.Group>
                                <Form.Group className="mb-3">
                                    <Form.Label>Descripción</Form.Label>
                                    <Form.Control
                                        as="textarea"
                                        rows={2}
                                        value={newProduct.description}
                                        onChange={e => setNewProduct({ ...newProduct, description: e.target.value })}
                                    />
                                </Form.Group>
                                <Row>
                                    <Col>
                                        <Form.Group className="mb-3">
                                            <Form.Label>Precio ($)</Form.Label>
                                            <Form.Control
                                                type="number"
                                                step="0.01"
                                                required
                                                value={newProduct.price}
                                                onChange={e => setNewProduct({ ...newProduct, price: e.target.value })}
                                            />
                                        </Form.Group>
                                    </Col>
                                    <Col>
                                        <Form.Group className="mb-3">
                                            <Form.Label>Stock</Form.Label>
                                            <Form.Control
                                                type="number"
                                                required
                                                value={newProduct.stock}
                                                onChange={e => setNewProduct({ ...newProduct, stock: e.target.value })}
                                            />
                                        </Form.Group>
                                    </Col>
                                </Row>
                                <Button variant="primary" type="submit" className="w-100 mt-2">
                                    Guardar Producto
                                </Button>
                            </Form>
                        </Card.Body>
                    </Card>
                </Col>

                {/* Column to View All Orders */}
                <Col lg={8}>
                    <Card className="shadow-sm border-0">
                        <Card.Body>
                            <Card.Title className="d-flex align-items-center mb-4">
                                <ShoppingBag className="me-2 text-primary" /> Todos los Pedidos
                            </Card.Title>
                            {loading ? (
                                <div className="text-center p-5"><Spinner animation="border" /></div>
                            ) : (
                                <Table responsive hover>
                                    <thead className="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Cliente</th>
                                            <th>Fecha</th>
                                            <th>Total</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {orders.map(order => (
                                            <tr key={order.id}>
                                                <td>#{order.id}</td>
                                                <td><code>{order.customerId}</code></td>
                                                <td>{new Date(order.createdAt).toLocaleDateString()}</td>
                                                <td className="fw-bold">${parseFloat(order.total).toFixed(2)}</td>
                                                <td>{getStatusBadge(order.status)}</td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </Table>
                            )}
                        </Card.Body>
                    </Card>
                </Col>
            </Row>
        </Container>
    );
};

export default AdminDashboard;
